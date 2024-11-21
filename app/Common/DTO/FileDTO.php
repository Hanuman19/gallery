<?php

namespace App\Common\DTO;

class FileDTO extends BaseDTO
{
    public ?string $error;
    public ?string $name;
    public ?string $type;
    public ?string $tmp_name;
    public ?string $size;
}
