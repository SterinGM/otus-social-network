<?php

namespace App\Service\User;

use App\DTO\User\Request\RegisterRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class Registration
{
    private UserPasswordHasherInterface $hasher;
    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $hasher, UserRepository $userRepository)
    {
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }

    public function registerUser(RegisterRequest $registerRequest): User {
        $user = $this->getUserFromDTO($registerRequest);

        $this->userRepository->createUser($user);

        return $user;
    }

    /**
     * @param RegisterRequest[] $requests
     */
    public function registerUsers(array $requests): void {
        $users = [];

        foreach ($requests as $registerRequest) {
            $users[] = $this->getUserFromDTO($registerRequest, false);
        }

        $this->userRepository->createUsers($users);
    }

    private function getUserFromDTO(RegisterRequest $registerDTO, $hashPassword = true): User
    {
        $birthdate = new DateTimeImmutable($registerDTO->birthdate);
        $user = new User()
            ->setId(Uuid::v7()->toRfc4122())
            ->setFirstName($registerDTO->firstName)
            ->setSecondName($registerDTO->secondName)
            ->setBirthdate($birthdate)
            ->setBiography($registerDTO->biography)
            ->setCity($registerDTO->city);

        $password = $hashPassword
            ? $this->hasher->hashPassword($user, $registerDTO->password)
            : $registerDTO->password;

        $user->setPassword($password);

        return $user;
    }
}