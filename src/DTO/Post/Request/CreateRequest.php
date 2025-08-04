<?php

namespace App\DTO\Post\Request;

class CreateRequest
{
    public const string FIELD_TEXT = 'text';

    public string $text;
    public string $authorId;

    private function __construct(string $text)
    {
        $this->text = $text;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_TEXT],
        );
    }
}