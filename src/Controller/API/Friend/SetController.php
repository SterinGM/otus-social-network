<?php

namespace App\Controller\API\Friend;

use App\DTO\Friend\Request\SetRequest;
use App\Service\ApiJsonResponse;
use App\Service\Friend\FriendInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SetController
{
    private FriendInterface $friend;

    public function __construct(FriendInterface $friend)
    {
        $this->friend = $friend;
    }

    #[Route('/friend/set/{user_id}', name: 'api_friend_set', methods: ['PUT'])]
    public function __invoke(UserInterface $user, SetRequest $setRequest): JsonResponse
    {
        $setRequest->fromUserId = $user->getUserIdentifier();

        $this->friend->setUserFriend($setRequest);

        return ApiJsonResponse::create();
    }
}