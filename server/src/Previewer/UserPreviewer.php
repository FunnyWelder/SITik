<?php

namespace App\Previewer;

use App\Entity\User;

class UserPreviewer
{
    public function preview(User $user): array
    {
        return [
            "id" => $user->getId(),
            "username" => $user->getUserIdentifier(),
            "roles" => $user->getRoles()
        ];
    }
}
