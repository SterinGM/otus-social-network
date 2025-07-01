<?php

namespace App\DTO\Auth\Response;

use App\Entity\ApiToken;

class LoginResponse
{
    public string $token;

    private function __construct(string $token)
    {
        $this->token = $token;
    }

    public static function createFromApiToken(ApiToken $apiToken): self
    {
        return new self(
            $apiToken->getToken(),
        );
    }
}