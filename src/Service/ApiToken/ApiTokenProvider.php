<?php

namespace App\Service\ApiToken;

use App\Service\ApiToken\Object\Token;
use Symfony\Component\Uid\Uuid;

class ApiTokenProvider implements ApiTokenProviderInterface
{
    private TokenRepositoryInterface $doctrineTokenRepository;

    public function __construct(TokenRepositoryInterface $doctrineTokenRepository)
    {
        $this->doctrineTokenRepository = $doctrineTokenRepository;
    }

    public function generateToken(string $userId): Token
    {
        $token = new Token(
            $this->newToken(),
            $userId
        );

        $this->doctrineTokenRepository->save($token);

        return $token;
    }

    public function getToken(string $token): Token
    {
        return $this->doctrineTokenRepository->getByToken($token);
    }

    private function newToken(): string    {
        return Uuid::v4()->toRfc4122();
    }
}