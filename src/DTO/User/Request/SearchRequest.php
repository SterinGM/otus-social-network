<?php

namespace App\DTO\User\Request;

class SearchRequest
{
    public const string FIELD_FIRST_NAME = 'first_name';
    public const string FIELD_LAST_NAME = 'last_name';

    public string $firstName;
    public string $lastName;

    private function __construct(
        string $firstName,
        string $lastName,
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_FIRST_NAME],
            $data[self::FIELD_LAST_NAME],
        );
    }
}