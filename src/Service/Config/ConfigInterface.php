<?php

namespace App\Service\Config;

interface ConfigInterface
{
    public const bool API_LOG_LOGIC = true;

    public function isEnabled(string $key);
}