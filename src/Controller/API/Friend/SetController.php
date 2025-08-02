<?php

namespace App\Controller\API\Friend;

use App\Service\ApiJsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SetController extends AbstractController
{
    #[Route('/friend/set/{user_id}', name: 'api_friend_set', methods: ['PUT'])]
    public function __invoke(UserInterface $user): JsonResponse
    {
        return ApiJsonResponse::create();
    }
}