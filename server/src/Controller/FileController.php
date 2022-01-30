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

#[Route('/files', name: 'files_')]
class FileController extends ApiController
{
    private EntityManagerInterface $em;
    private FileRepository $fileRepository;
    private UserRepository $userRepository;
    private string $targetDirectory;

    public function __construct(
        EntityManagerInterface $em,
        FileRepository $fileRepository,
        UserRepository $userRepository,
        string $targetDirectory,
    ) {
        $this->em = $em;
        $this->fileRepository = $fileRepository;
        $this->userRepository = $userRepository;
        $this->targetDirectory = $targetDirectory;
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request, FileUploader $fileUploader): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);

        $uploadedFile = $request->files->get('uploadedFile');
        if (!isset($uploadedFile)) {
            return $this->respondValidationError("File not set");
        }

        $request = $request->request->all();

        $fileExist = (bool)$this->fileRepository->findOneBy(['author'=>$author, 'name' => $request['filename']]);
        if ($fileExist) {
            return $this->respondValidationError("File with this name is already exist");
        }

        $fileSize = $uploadedFile->getSize();
        $fileName = $fileUploader->upload($uploadedFile);
        if (is_null($fileName)) {
            return $this->respondWithErrors("File not uploaded");
        }

        $file = new File();

        try {
            $file
                ->setName($request['filename'])
                ->setUrl($fileName)
                ->setDateCreated(new DateTime())
                ->setAuthor($author)
                ->setSize($fileSize);

            $this->em->persist($file);
            $this->em->flush();

            return $this->respondWithSuccess("File added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route(name: 'show_all', methods: ['GET'])]
    public function showAllSelf(FilePreviewer $filePreviewer): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $files = $this->fileRepository->findBy(['author'=>$author]);

        $this->em->getFilters()->disable('softdeleteable');

        $data = [];
        foreach ($files as $file) {
            $data[] = $filePreviewer->preview($file);
        }

        return $this->response($data);
    }

    #[Route('/{filename}', name: 'show', methods: ['GET'])]
    public function showSelf($filename): BinaryFileResponse|JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $file = $this->fileRepository->findOneBy(['author'=>$author, 'name'=>$filename]);
        if (!$file) {
            return $this->respondNotFound("File not found");
        }

        return new BinaryFileResponse($this->targetDirectory . $file->getUrl());
    }

    #[Route('/{filename}', name: 'delete', methods: ['DELETE'])]
    public function deleteSelf($filename): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $file = $this->fileRepository->findOneBy(['author'=>$author, 'name'=>$filename]);
        if (!$file) {
            return $this->respondNotFound("File not found");
        }

        $filesystem = new Filesystem();
        try {
            $filesystem->remove([$this->targetDirectory . $file->getUrl()]);
        } catch (IOExceptionInterface) {
            return $this->respondWithErrors("Undeletable file!");
        }

        $this->em->remove($file);
        $this->em->flush();

        return $this->respondWithSuccess("File deleted successfully");
    }
}
