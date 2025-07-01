<?php

namespace App\DTO\User\Response;

use App\Entity\User;

class RegisterResponse
{
    public string $userId;

    private function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public static function createFromUser(User $user): self
    {
        return new self($user->getId());
    }
}