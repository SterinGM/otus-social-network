<?php

namespace App\Service\ApiToken;

use App\Entity\Main\ApiToken;
use App\Repository\Main\ApiTokenRepository;
use App\Service\ApiToken\Object\Token;
use App\Service\Exception\InvalidCredentialsException;

class DoctrineTokenRepository implements TokenRepositoryInterface
{
    private ApiTokenRepository $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function save(Token $token): void
    {
        $apiToken = $this->buildApiToken($token);

        $this->apiTokenRepository->clearUserTokens($token->userId);
        $this->apiTokenRepository->create($apiToken);
    }

    public function getByToken(string $token): Token
    {
        $apiToken = $this->apiTokenRepository->getByToken($token);

        if (!$apiToken) {
            throw new InvalidCredentialsException();
        }

        return $this->mapToken($apiToken);
    }

    private function buildApiToken(Token $token): ApiToken
    {
        return new ApiToken()
            ->setToken($token->token)
            ->setUserId($token->userId);
    }

    private function mapToken(ApiToken $apiToken): Token
    {
        return new Token(
            $apiToken->getToken(),
            $apiToken->getUserId(),
        );
    }
}