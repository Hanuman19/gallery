<?php

namespace App\Common\DTO;

class BaseDTO
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}
