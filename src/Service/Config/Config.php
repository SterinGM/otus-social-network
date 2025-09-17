<?php

namespace App\Service\Config;

class Config implements ConfigInterface
{
    private bool $apiLogLogic;
    private string $authTokenSchema;

    public function __construct(
        bool $apiLogLogic,
        string $authTokenSchema,
    ) {
        $this->apiLogLogic = $apiLogLogic;
        $this->authTokenSchema = $authTokenSchema;
    }

    public function isEnabled(string $key): bool
    {
        return match ($key) {
            self::API_LOG_LOGIC => $this->apiLogLogic,
            default => false,
        };
    }

    public function getStringValue(string $key): string
    {
        return match ($key) {
            self::AUTH_TOKEN_SCHEMA => $this->authTokenSchema,
            default => '',
        };
    }
}