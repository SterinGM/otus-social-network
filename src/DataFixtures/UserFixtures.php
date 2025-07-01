<?php

namespace App\DataFixtures;

use App\DTO\User\Request\RegisterRequest;
use App\Service\User\Registration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private const array TEST_USER = [
        'firstName' => 'Григорий',
        'secondName' => 'Стерин',
        'birthdate' => '1981-12-01',
        'biography' => 'Житель Кенигсберга',
        'city' => 'Калининград',
        'password' => '123123123',
    ];

    private Registration $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    public function load(ObjectManager $manager): void
    {
        $registerRequest = RegisterRequest::createFromArray(self::TEST_USER);

        $this->registration->registerUser($registerRequest);
    }
}
