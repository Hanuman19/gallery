<?php

namespace App\Repository\Gallery\DTO;

use App\Common\DTO\BaseDTO;

class SaveFileDTO extends BaseDTO
{
    public string $title;
    public ?string $description;
    public string $path;
}
