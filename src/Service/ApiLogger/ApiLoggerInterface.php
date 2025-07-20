<?php

namespace App\Service\ApiLogger;

use Symfony\Component\HttpFoundation\Request;

interface ApiLoggerInterface
{
    public function log(ApiData $data): void;

    public function generateId(Request $request): void;
}