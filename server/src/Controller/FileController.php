<?php

namespace App\Controller;

use App\Entity\File;
use App\Previewer\FilePreviewer;
use App\Repository\FileRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/file', name: 'file_')]
class FileController extends ApiController
{
    private EntityManagerInterface $em;
    private FileRepository $fileRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        FileRepository $fileRepository,
        UserRepository $userRepository,
    ){
        $this->em = $em;
        $this->fileRepository = $fileRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/files', name: 'new', methods: ['POST'])]
    public function new(Request $request, FileUploader $fileUploader): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);

        $uploadedFile = $request->files->get('uploadedFile');
        if (!isset($uploadedFile)) {
            return $this->respondValidationError("File not set");
        }

        $fileName = $fileUploader->upload($uploadedFile);
        if (is_null($fileName)){
            return $this->respondWithErrors("File not uploaded");
        }

        $request = $request->request->all();
        $file = new File();

        try {
            $fileExist = (bool)$this->fileRepository->findOneBy(['author'=>$author, 'name' => $request['filename']]);
            if (!$fileExist) {
                return $this->respondValidationError("File with this name is already exist");
            }

            $file
                ->setName($request['filename'])
                ->setUrl($fileName)
                ->setDateCreated(new DateTime())
                ->setAuthor($author);

            $this->em->persist($file);
            $this->em->flush();

            return $this->respondWithSuccess("File added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/files', name: 'show', methods: ['GET'])]
    public function showAllSelf(FilePreviewer $filePreviewer): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $files = $this->fileRepository->findBy(['author'=>$author]);

        $this->em->getFilters()->disable('softdeleteable');

        $data = [];
        foreach ($files as $file) {
            $data[] = $this->response($filePreviewer->preview($file));
        }

        return $this->response($data);
    }

    #[Route('/files/{filename}', name: 'show', methods: ['GET'])]
    public function showSelf($filename): BinaryFileResponse|JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $file = $this->fileRepository->findOneBy(['author'=>$author, 'name'=>$filename]);
        if (!$file) {
            return $this->respondNotFound("File not found");
        }

        return new BinaryFileResponse($file->getUrl());
    }

    #[Route('/files/{filename}', name: 'delete_self', methods: ['DELETE'])]
    public function deleteSelf($filename): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $file = $this->fileRepository->findOneBy(['author'=>$author, 'name'=>$filename]);
        if (!$file) {
            return $this->respondNotFound("File not found");
        }

        $filesystem = new Filesystem();
        try {
            $filesystem->remove([$file->getUrl()]);
        } catch (IOExceptionInterface) {
            return $this->respondWithErrors("Undeletable file");
        }

        $this->em->remove($file);
        $this->em->flush();

        return $this->respondWithSuccess("File deleted successfully");
    }
}
