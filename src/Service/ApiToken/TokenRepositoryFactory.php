<?php

namespace App\Service\ApiToken;

use App\Service\Config\ConfigInterface;
use RuntimeException;

class TokenRepositoryFactory
{
    private const string SCHEMA_DOCTRINE = 'doctrine';
    private const string SCHEMA_REDIS = 'redis';

    private ConfigInterface $config;
    private TokenRepositoryInterface $doctrineTokenRepository;
    private TokenRepositoryInterface $redisTokenRepository;

    public function __construct(
        ConfigInterface $config,
        TokenRepositoryInterface $doctrineTokenRepository,
        TokenRepositoryInterface $redisTokenRepository,
    ) {
        $this->config = $config;
        $this->doctrineTokenRepository = $doctrineTokenRepository;
        $this->redisTokenRepository = $redisTokenRepository;
    }

    public function getRepository(): TokenRepositoryInterface
    {
        $schema = $this->config->getStringValue(ConfigInterface::AUTH_TOKEN_SCHEMA);

        return match ($schema) {
            self::SCHEMA_DOCTRINE => $this->doctrineTokenRepository,
            self::SCHEMA_REDIS => $this->redisTokenRepository,
            default => throw new RuntimeException('Unknown auth schema'),
        };
    }
}