<?php

namespace App\DTO\User;

use App\Entity\User as UserEntity;

class User
{
    private const string BIRTHDATE_FORMAT = 'Y-m-d';

    public string $id;
    public string $firstName;
    public string $secondName;
    public string $birthdate;
    public string $biography;
    public string $city;

    private function __construct(
        string $id,
        string $firstName,
        string $secondName,
        string $birthdate,
        string $biography,
        string $city,
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->secondName = $secondName;
        $this->birthdate = $birthdate;
        $this->biography = $biography;
        $this->city = $city;
    }

    public static function createFromUser(UserEntity $user): self
    {
        return new self(
            $user->getId(),
            $user->getFirstName(),
            $user->getSecondName(),
            $user->getBirthdate()->format(self::BIRTHDATE_FORMAT),
            $user->getBiography(),
            $user->getCity(),
        );
    }
}