<?php

namespace App\Service\ApiLogger;

interface ApiLoggerInterface
{
    public function log(ApiData $data): void;
}