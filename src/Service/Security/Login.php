<?php

namespace App\Service\Security;

use App\DTO\Auth\Request\LoginRequest;
use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use Symfony\Component\Uid\Uuid;

class Login
{
    private ApiTokenRepository $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function loginUser(LoginRequest $loginRequest): ApiToken
    {
        $apiToken = $this->getTokenFromRequest($loginRequest);

        $this->apiTokenRepository->create($apiToken);

        return $apiToken;
    }

    private function getTokenFromRequest(LoginRequest $loginRequest): ApiToken
    {
        return new ApiToken()
            ->setToken(Uuid::v7()->toRfc4122())
            ->setUserId($loginRequest->userId);
    }
}