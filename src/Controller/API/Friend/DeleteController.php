<?php

namespace App\Controller\API\Friend;

use App\DTO\Friend\Request\DeleteRequest;
use App\Service\ApiJsonResponse;
use App\Service\Friend\FriendInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DeleteController extends AbstractController
{
    private FriendInterface $friend;

    public function __construct(FriendInterface $friend)
    {
        $this->friend = $friend;
    }

    #[Route('/friend/delete/{user_id}', name: 'api_friend_delete', methods: ['PUT'])]
    public function __invoke(UserInterface $user, DeleteRequest $deleteRequest): JsonResponse
    {
        $deleteRequest->fromUserId = $user->getId();

        $this->friend->deleteUserFriend($deleteRequest);

        return ApiJsonResponse::create();
    }
}