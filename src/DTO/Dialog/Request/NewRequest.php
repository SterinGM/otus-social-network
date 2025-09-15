<?php

namespace App\DTO\Dialog\Request;

class NewRequest
{
    public const string FIELD_USERS = 'users';

    /** @var string[] $users */
    public array $users;

    /**
     * @param string[] $users
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data[self::FIELD_USERS],
        );
    }
}