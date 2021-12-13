<?php

namespace App\Controller;

use App\Entity\User;
use App\Previewer\UserPreviewer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user_')]
class UserController extends ApiController
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository
    ){
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(UserPasswordHasherInterface $passwordEncoder, Request $request): JsonResponse
    {
        $request = $request->request->all();
        $user = new User();

        $userExist = (bool)$this->userRepository->findOneBy(['username' => $request['username']]);
        if ($userExist) {
            return $this->respondValidationError('User with this username is already exist');
        }

        try {
            if ($request['username'] == "" || $request['password'] == ""){
                throw new Exception();
            }

            $user
                ->setUsername($request['username'])
                ->setPassword($passwordEncoder->hashPassword(
                    $user,
                    $request['password']
                ));

            if (isset($request['roles'])) {
                $user->setRoles($request['roles']);
            }

            $this->em->persist($user);
            $this->em->flush();

            return $this->respondWithSuccess("User added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/{user_id}', name: 'show', requirements: ['user_id' => '\d+'], methods: ['GET'])]
    public function show(UserPreviewer $userPreviewer, $user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        return $this->response($userPreviewer->preview($user));
    }

    #[Route('/{user_id}', name: 'edit', requirements: ['user_id' => '\d+'], methods: ['PUT'])]
    public function edit(UserPasswordHasherInterface $passwordEncoder, Request $request, $user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        $request = $request->request->all();

        try {
            if (isset($request['username'])) {
                if ($request['username'] == "") {
                    throw new Exception();
                }
                $userExist = (bool)$this->userRepository->findOneBy(['username' => $request['username']]);
                if ($userExist) {
                    return $this->respondValidationError('User with this login is already exist');
                }
                $user->setUsername($request['username']);
            }
            if (isset($request['password'])) {
                $user->setPassword($passwordEncoder->hashPassword(
                    $user,
                    $request['password']
                ));
            }
            if (isset($request['roles'])) {
                $user->setRoles($request['roles']);
            }

            $this->em->flush();

            return $this->respondWithSuccess("User updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/{user_id}', name: 'delete', requirements: ['user_id' => '\d+'], methods: ['DELETE'])]
    public function delete($user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->respondWithSuccess("User deleted successfully");
    }
}
