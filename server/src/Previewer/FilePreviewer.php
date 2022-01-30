<?php

namespace App\Previewer;

use App\Entity\File;

class FilePreviewer
{
    public function preview(File $file): array
    {
        return [
            "id" => $file->getId(),
            "filename" => $file->getName(),
            "url" => $file->getUrl(),
            "date_created" => $file->getDateCreated(),
        ];
    }
}