<?php

namespace App\DataFixtures;

use App\DTO\User\Request\RegisterRequest;
use App\Service\User\Registration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

ini_set('memory_limit', -1);

class UserFixtures extends Fixture
{
    public const string USER_REFERENCE = 'user';

    private const array TEST_USER = [
        RegisterRequest::FIELD_FIRST_NAME => 'Григорий',
        RegisterRequest::FIELD_SECOND_NAME => 'Стерин',
        RegisterRequest::FIELD_BIRTH_DATE => '1981-12-01',
        RegisterRequest::FIELD_BIOGRAPHY => 'Житель Кенигсберга',
        RegisterRequest::FIELD_CITY => 'Калининград',
        RegisterRequest::FIELD_PASSWORD => '123123123',
    ];

    private EntityManagerInterface $entityManager;
    private Registration $registration;
    private Generator $faker;

    public function __construct(EntityManagerInterface $entityManager, Registration $registration)
    {
        $this->registration = $registration;
        $this->faker = Factory::create('ru_RU');

        $this->entityManager = $entityManager;
        $this->entityManager->getConnection()->getConfiguration()->setMiddlewares([]);
    }

    public function load(ObjectManager $manager): void
    {
        $registerRequest = RegisterRequest::createFromArray(self::TEST_USER);

        $user = $this->registration->registerUser($registerRequest);

        $this->addReference(self::USER_REFERENCE, $user);

        for ($i = 0; $i < 10_000; $i++) {
            $requests = [];

            for ($j = 0; $j < 100; $j++) {
                $gender = $this->faker->randomElement(['male', 'female']);
                $data = [
                    RegisterRequest::FIELD_FIRST_NAME => $this->faker->firstName($gender),
                    RegisterRequest::FIELD_SECOND_NAME => $this->faker->lastName($gender),
                    RegisterRequest::FIELD_BIRTH_DATE => $this->faker->date,
                    RegisterRequest::FIELD_BIOGRAPHY => $this->faker->realText,
                    RegisterRequest::FIELD_CITY => $this->faker->city,
                    RegisterRequest::FIELD_PASSWORD => $this->faker->password,
                ];

                $requests[] = RegisterRequest::createFromArray($data);
            }

            $this->registration->registerUsers($requests);

            $this->entityManager->clear();
        }
    }
}
