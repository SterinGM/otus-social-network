<?php

namespace App\DataFixtures;

use App\DTO\Post\Request\CreateRequest;
use App\Repository\Main\UserRepository;
use App\Service\Post\PostProviderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;
    private PostProviderInterface $postProvider;
    private Generator $faker;

    public function __construct(UserRepository $userRepository, PostProviderInterface $postProvider)
    {
        $this->userRepository = $userRepository;
        $this->postProvider = $postProvider;
        $this->faker = Factory::create('ru_RU');
    }

    public function load(ObjectManager $manager): void
    {
        $userIds = $this->userRepository->getIdList(1_000);

        foreach ($userIds as $userId) {
            for ($i = 0; $i < $this->faker->numberBetween(1, 10); $i++) {
                $creatPostRequest = CreateRequest::createFromArray([
                    CreateRequest::FIELD_TEXT => $this->faker->realText,
                ]);
                $creatPostRequest->authorId = $userId;

                $this->postProvider->create($creatPostRequest);
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
