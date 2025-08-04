<?php

namespace App\DataFixtures;

use App\DTO\Friend\Request\SetRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Friend\Friend;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FriendFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;
    private Friend $friend;

    public function __construct(UserRepository $userRepository, Friend $friend)
    {
        $this->userRepository = $userRepository;
        $this->friend = $friend;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference(UserFixtures::USER_REFERENCE, User::class);
        $userIds = $this->userRepository->getIdList();

        foreach ($userIds as $userId) {
            $friendRequest = SetRequest::createFromArray([
                SetRequest::FIELD_USER_ID => $userId,
            ]);
            $friendRequest->fromUserId = $user->getId();

            $this->friend->setUserFriend($friendRequest);
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
