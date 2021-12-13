<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Previewer\TodoPreviewer;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo', name: 'todo_')]
class TodoController extends ApiController
{
    private EntityManagerInterface $em;
    private TodoRepository $todoRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        TodoRepository $todoRepository,
        UserRepository $userRepository,
    ){
        $this->em = $em;
        $this->todoRepository = $todoRepository;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $request = $request->request->all();
        $todo = new todo();

        try {
            $author = $this->userRepository->find($request['author_id']);
            if (!$author) {
                return $this->respondNotFound("Author not found");
            }

            $todo
                ->setDescription($request['description'])
                ->setDone(false)
                ->setDateCreated(new DateTime())
                ->setAuthor($author);

            $this->em->persist($todo);
            $this->em->flush();

            return $this->respondWithSuccess("Todo added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/{todo_id}', name: 'show', requirements: ['todo_id' => '\d+'], methods: ['GET'])]
    public function show(TodoPreviewer $todoPreviewer, $todo_id): JsonResponse
    {
        $todo = $this->todoRepository->find($todo_id);
        if (!$todo) {
            return $this->respondNotFound("Todo not found");
        }

        $this->em->getFilters()->disable('softdeleteable');

        return $this->response($todoPreviewer->preview($todo));
    }

    #[Route('/{todo_id}', name: 'edit', requirements: ['todo_id' => '\d+'], methods: ['PUT'])]
    public function edit(Request $request, $todo_id): JsonResponse
    {
        $todo = $this->todoRepository->find($todo_id);
        if (!$todo) {
            return $this->respondNotFound("Todo not found");
        }

        $request = $request->request->all();

        try {
            if (isset($request['description'])) {
                $todo->setDescription($request['description']);
            }
            if (isset($request['done'])) {
                $todo->setDone($request['done']);
            }
            if (isset($request['author_id'])) {
                $author = $this->userRepository->find($request['author_id']);
                if (!$author) {
                    return $this->respondNotFound("Author not found");
                }
                $todo->setAuthor($author);
            }

            $this->em->flush();

            return $this->respondWithSuccess("Todo updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/{todo_id}', name: 'delete', requirements: ['todo_id' => '\d+'], methods: ['DELETE'])]
    public function delete($todo_id): JsonResponse
    {
        $todo = $this->todoRepository->find($todo_id);
        if (!$todo) {
            return $this->respondNotFound("Todo not found");
        }

        $this->em->remove($todo);
        $this->em->flush();

        return $this->respondWithSuccess("Todo deleted successfully");
    }

    #[Route('/self', name: 'show_self_all', methods: ['GET'])]
    public function showSelfAll(TodoPreviewer $todoPreviewer): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $todos = $this->todoRepository->findBy(['author' => $user]);

        $arrayTodo = [];
        foreach ($todos as $todo) {
            $arrayTodo[] = $todoPreviewer->previewSelf($todo);
        }

        return $this->response($arrayTodo);
    }

    #[Route('/self', name: 'new_self', methods: ['POST'])]
    public function newSelf(Request $request): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);

        $request = $request->request->all();
        $todo = new todo();

        try {
            $todo
                ->setDescription($request['description'])
                ->setDone(false)
                ->setDateCreated(new DateTime())
                ->setAuthor($author);

            $this->em->persist($todo);
            $this->em->flush();

            return $this->respondWithSuccess("Todo added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/self/{todo_id}', name: 'edit_self', requirements: ['todo_id' => '\d+'], methods: ['PUT'])]
    public function editSelf(Request $request, $todo_id): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $todo = $this->todoRepository->findOneBy(['author'=>$author, 'id'=>$todo_id]);
        if (!$todo) {
            return $this->respondNotFound("Todo not found");
        }

        $request = $request->request->all();

        try {
            if (isset($request['description'])) {
                $todo->setDescription($request['description']);
            }
            if (isset($request['done'])) {
                $todo->setDone($request['done']);
            }

            $this->em->flush();

            return $this->respondWithSuccess("Todo updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/self/{todo_id}', name: 'delete_self', requirements: ['todo_id' => '\d+'], methods: ['DELETE'])]
    public function deleteSelf($todo_id): JsonResponse
    {
        $author = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);
        $todo = $this->todoRepository->findOneBy(['author'=>$author, 'id'=>$todo_id]);
        if (!$todo) {
            return $this->respondNotFound("Todo not found");
        }

        $this->em->remove($todo);
        $this->em->flush();

        return $this->respondWithSuccess("Todo deleted successfully");
    }
}
