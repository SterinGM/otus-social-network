<?php

namespace App\DTO\Post\Request;

class UpdateRequest
{
    public const string FIELD_ID = 'id';
    public const string FIELD_TEXT = 'text';

    public string $id;
    public string $text;
    public string $authorId;

    public function __construct(string $id, string $text)
    {
        $this->id = $id;
        $this->text = $text;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_ID],
            $data[self::FIELD_TEXT],
        );
    }
}