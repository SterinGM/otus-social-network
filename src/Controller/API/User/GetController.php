<?php

namespace App\Controller\API\User;

use App\DTO\User\Response\GetResponse;
use App\Service\ApiJsonResponse;
use App\Service\User\Profile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetController
{
    private Profile $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    #[Route('/user/get/{id}', name: 'api_user_get', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        $user = $this->profile->getProfile($id);

        return ApiJsonResponse::create(GetResponse::createFromUser($user));
    }
}