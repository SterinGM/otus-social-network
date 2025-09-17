<?php

namespace App\Service\Config;

interface ConfigInterface
{
    public const string API_LOG_LOGIC = 'api_log_logic';
    public const string AUTH_TOKEN_SCHEMA = 'auth_token_schema';

    public function isEnabled(string $key): bool;

    public function getStringValue(string $key): string;
}