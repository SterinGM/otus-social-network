<?php

namespace App\Service\ApiToken;

use App\Service\ApiToken\Object\Token;
use Symfony\Component\Uid\Uuid;

class ApiTokenProvider implements ApiTokenProviderInterface
{
    private TokenRepositoryInterface $tokenRepository;

    public function __construct(
        TokenRepositoryFactory $tokenRepositoryFactory
    ) {
        $this->tokenRepository = $tokenRepositoryFactory->getRepository();
    }

    public function generateToken(string $userId): Token
    {
        $token = new Token(
            $this->newToken(),
            $userId
        );

        $this->tokenRepository->save($token);

        return $token;
    }

    public function getToken(string $token): Token
    {
        return $this->tokenRepository->getByToken($token);
    }

    private function newToken(): string    {
        return Uuid::v4()->toRfc4122();
    }
}