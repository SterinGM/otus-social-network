<?php

namespace App\Service\Config;

class Config implements ConfigInterface
{
    private bool $apiLogLogic;

    public function __construct(bool $apiLogLogic)
    {
        $this->apiLogLogic = $apiLogLogic;
    }

    public function isEnabled(string $key): bool
    {
        return match ($key) {
            self::API_LOG_LOGIC => $this->apiLogLogic,
            default => false,
        };
    }
}