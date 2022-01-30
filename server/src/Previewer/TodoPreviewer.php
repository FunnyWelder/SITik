<?php

namespace App\Previewer;

use App\Entity\Todo;

class TodoPreviewer
{
    private UserPreviewer $userPreviewer;

    public function __construct(
        UserPreviewer $userPreviewer
    ) {
        $this->userPreviewer = $userPreviewer;
    }

    public function preview(Todo $todo): array
    {
        return [
            "id" => $todo->getId(),
            "username" => $todo->getDateCreated(),
            "description" => $todo->getDescription(),
            "done" => $todo->getDone(),
            "author" => $this->userPreviewer->preview($todo->getAuthor())
        ];
    }

    public function previewSelf(Todo $todo): array
    {
        return [
            "id" => $todo->getId(),
            "username" => $todo->getDateCreated(),
            "description" => $todo->getDescription(),
            "done" => $todo->getDone()
        ];
    }
}
