<?php

namespace App\Http\Response;

class BaseResponse
{
    public bool $success = false;
    /**
     * @var string[]
     */
    public array $messages = [];

    public function setMsg($msg): void
    {
        if (is_array($msg)) {
            $this->messages = array_merge($this->messages, $msg);
        }

        if (is_string($msg)) {
            $this->messages[] = $msg;
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
