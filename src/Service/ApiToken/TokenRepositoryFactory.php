<?php

namespace App\Service\ApiToken;

class TokenRepositoryFactory
{
    private TokenRepositoryInterface $doctrineTokenRepository;
    private TokenRepositoryInterface $redisTokenRepository;

    public function __construct(
        TokenRepositoryInterface $doctrineTokenRepository,
        TokenRepositoryInterface $redisTokenRepository,
    ) {
        $this->doctrineTokenRepository = $doctrineTokenRepository;
        $this->redisTokenRepository = $redisTokenRepository;
    }

    public function getRepository(): TokenRepositoryInterface
    {
        return $this->redisTokenRepository;
    }
}