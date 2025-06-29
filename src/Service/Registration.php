<?php

namespace App\Service;

use App\DTO\RegisterDTO;
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

    public function registerUser(RegisterDTO $registerDTO): string {
        $user = $this->getUserFromDTO($registerDTO);

        $this->userRepository->createUser($user);

        return $user->getUserIdentifier();
    }

    private function getUserFromDTO(RegisterDTO $registerDTO): User
    {
        $birthdate = new DateTimeImmutable($registerDTO->birthdate);
        $user = new User()
            ->setId(Uuid::v7()->toRfc4122())
            ->setFirstName($registerDTO->firstName)
            ->setSecondName($registerDTO->secondName)
            ->setBirthdate($birthdate)
            ->setBiography($registerDTO->biography)
            ->setCity($registerDTO->city);

        $password = $this->hasher->hashPassword($user, $registerDTO->password);
        $user->setPassword($password);

        return $user;
    }
}