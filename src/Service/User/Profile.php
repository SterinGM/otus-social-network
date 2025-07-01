<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Exception\UserNotFoundException;
use DateTimeImmutable;

class Profile
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getProfile(string $id): User
    {
        $data = $this->userRepository->getById($id);

        if ($data === null) {
            throw new UserNotFoundException($id);
        }

        return $this->mapUser($data);
    }

    private function mapUser(array $data): User
    {
        $birthdate = DateTimeImmutable::createFromFormat(UserRepository::BIRTHDATE_FORMAT, $data['birthdate']);

        return new User()
            ->setId($data['id'])
            ->setFirstName($data['first_name'])
            ->setSecondName($data['second_name'])
            ->setBirthdate($birthdate)
            ->setBiography($data['biography'])
            ->setCity($data['city']);
    }
}