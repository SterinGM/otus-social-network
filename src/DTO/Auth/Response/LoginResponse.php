<?php

namespace App\DTO\Auth\Response;

use App\Service\ApiToken\Object\Token;

class LoginResponse
{
    public string $token;

    private function __construct(string $token)
    {
        $this->token = $token;
    }

    public static function createFromApiToken(Token $apiToken): self
    {
        return new self(
            $apiToken->token,
        );
    }
}