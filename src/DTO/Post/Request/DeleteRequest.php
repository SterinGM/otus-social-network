<?php

namespace App\DTO\Post\Request;

class DeleteRequest
{
    public const string FIELD_ID = 'id';

    public string $id;
    public string $authorId;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_ID],
        );
    }
}