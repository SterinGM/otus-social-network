<?php

namespace App\Service\Security;

use App\DTO\Auth\Request\LoginRequest;
use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use App\Service\Exception\InvalidCredentialsException;
use App\Service\Exception\UserNotFoundException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class Login
{
    private ApiTokenRepository $apiTokenRepository;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        ApiTokenRepository $apiTokenRepository,
        UserRepository $userRepository,
        UserPasswordHasherInterface $hasher
    ) {
        $this->apiTokenRepository = $apiTokenRepository;
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    public function loginUser(LoginRequest $loginRequest): ApiToken
    {
        $user = $this->userRepository->getById($loginRequest->userId);

        if ($user === null) {
            throw new UserNotFoundException($loginRequest->userId);
        }

        if (!$this->hasher->isPasswordValid($user, $loginRequest->password)) {
            throw new InvalidCredentialsException();
        }

        $this->apiTokenRepository->clearUserTokens($user->getId());

        $apiToken = $this->getTokenFromRequest($loginRequest);

        $this->apiTokenRepository->create($apiToken);

        return $apiToken;
    }

    private function getTokenFromRequest(LoginRequest $loginRequest): ApiToken
    {
        return new ApiToken()
            ->setToken(Uuid::v4()->toRfc4122())
            ->setUserId($loginRequest->userId);
    }
}