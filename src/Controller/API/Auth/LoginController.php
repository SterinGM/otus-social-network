<?php

namespace App\Controller\API\Auth;

use App\DTO\Auth\Request\LoginRequest;
use App\DTO\Auth\Response\LoginResponse;
use App\Service\ApiJsonResponse;
use App\Service\Security\Login;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LoginController
{
    private Login $login;

    public function __construct(Login $login)
    {
        $this->login = $login;
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function __invoke(LoginRequest $loginRequest): JsonResponse
    {
        $apiToken = $this->login->loginUser($loginRequest);

        return ApiJsonResponse::create(LoginResponse::createFromApiToken($apiToken));
    }
}