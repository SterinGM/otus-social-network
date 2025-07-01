<?php

namespace App\Controller\API\User;

use App\DTO\User\Request\RegisterRequest;
use App\DTO\User\Response\RegisterResponse;
use App\Service\ApiJsonResponse;
use App\Service\User\Registration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController
{
    private Registration $registration;

    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    #[Route('/user/register', name: 'api_user_register', methods: ['POST'])]
    public function __invoke(RegisterRequest $registerRequest): JsonResponse
    {
        $user = $this->registration->registerUser($registerRequest);

        return ApiJsonResponse::create(RegisterResponse::createFromUser($user));
    }
}