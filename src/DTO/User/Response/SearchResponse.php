<?php

namespace App\DTO\User\Response;

use App\DTO\User\User;
use App\Entity\Main\User as UserEntity;

class SearchResponse
{
    /**
     * @var User[]
     */
    public array $users;

    /**
     * @param User[] $users
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * @param UserEntity[] $entityUsers
     * @return self
     */
    public static function createFromResult(array $entityUsers): self
    {
        $users = [];

        foreach ($entityUsers as $user) {
            $users[] = User::createFromUser($user);
        }

        return new self($users);
    }
}