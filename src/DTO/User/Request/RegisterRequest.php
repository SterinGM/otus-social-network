<?php

namespace App\DTO\User\Request;

class RegisterRequest
{
    public const string FIELD_FIRST_NAME = 'first_name';
    public const string FIELD_SECOND_NAME = 'second_name';
    public const string FIELD_BIRTH_DATE = 'birthdate';
    public const string FIELD_BIOGRAPHY = 'biography';
    public const string FIELD_CITY = 'city';
    public const string FIELD_PASSWORD = 'password';

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
            $data[self::FIELD_FIRST_NAME],
            $data[self::FIELD_SECOND_NAME],
            $data[self::FIELD_BIRTH_DATE],
            $data[self::FIELD_BIOGRAPHY],
            $data[self::FIELD_CITY],
            $data[self::FIELD_PASSWORD],
        );
    }
}