<?php

namespace App\DTO;

use App\Service\ErrorSystem\ErrorCode;

readonly class Error
{
    public int $code;
    public string $message;
    public array $data;

    private function __construct(int $code, string $message, array $data = [])
    {
        $this->message = $message;
        $this->data = $data;
        $this->code = $code;
    }

    public static function create(ErrorCode $code, string $error, array $data = []): self
    {
        return new self($code->value, $error, $data);
    }
}