<?php

namespace App\Service\Security;

use App\DTO\Auth\Request\LoginRequest;
use App\Repository\Main\UserRepository;
use App\Service\ApiToken\ApiTokenProviderInterface;
use App\Service\ApiToken\Object\Token;
use App\Service\Exception\InvalidCredentialsException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Login
{
    private ApiTokenProviderInterface $apiTokenProvider;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        ApiTokenProviderInterface $apiTokenProvider,
        UserRepository $userRepository,
        UserPasswordHasherInterface $hasher
    ) {
        $this->apiTokenProvider = $apiTokenProvider;
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    public function loginUser(LoginRequest $loginRequest): Token
    {
        $user = $this->userRepository->getById($loginRequest->userId);

        if ($user === null) {
            throw new InvalidCredentialsException();
        }

        if (!$this->hasher->isPasswordValid($user, $loginRequest->password)) {
            throw new InvalidCredentialsException();
        }

        return $this->apiTokenProvider->generateToken($user->getId());
    }
}