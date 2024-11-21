<?php

namespace App\Services\Gallery\DTO;

use App\Common\DTO\BaseDTO;
use App\Common\DTO\FileDTO;

class GalleryServiceUploadDTO extends BaseDTO
{
    public FileDTO $file;
    public string $title;
    public ?string $description;
}
