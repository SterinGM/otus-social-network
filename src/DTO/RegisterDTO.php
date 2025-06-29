<?php

namespace App\DTO;

class RegisterDTO
{
    public string $firstName;
    public string $secondName;
    public string $birthdate;
    public string $biography;
    public string $city;
    public string $password;

    private function __construct(
        string $firstName,
        string $secondName,
        string $birthdate,
        string $biography,
        string $city,
        string $password
    ) {
        $this->firstName = $firstName;
        $this->secondName = $secondName;
        $this->birthdate = $birthdate;
        $this->biography = $biography;
        $this->city = $city;
        $this->password = $password;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['firstName'],
            $data['secondName'],
            $data['birthdate'],
            $data['biography'],
            $data['city'],
            $data['password']
        );
    }
}