<?php

namespace App\Service\ApiToken;

use App\Service\ApiToken\Object\Token;

interface ApiTokenProviderInterface
{
    public function generateToken(string $userId): Token;

    public function getToken(string $token): Token;
}