<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Exception\UserNotFoundException;

class Profile
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProfile(string $id): User
    {
        $user = $this->userRepository->getById($id);

        if ($user === null) {
            throw new UserNotFoundException($id);
        }

        return $user;
    }
}