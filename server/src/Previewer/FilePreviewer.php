<?php

namespace App\Previewer;

use App\Entity\File;

class FilePreviewer
{
    private string $targetDirectory;

    public function __construct(
        string $targetDirectory
    ) {
        $this->targetDirectory = $targetDirectory;
    }

    public function preview(File $file): array
    {
        return [
            "id" => $file->getId(),
            "filename" => $file->getName(),
            "size" => $file->getSize(),
            "url" => $this->targetDirectory . $file->getUrl(),
            "date_created" => $file->getDateCreated(),
        ];
    }
}
