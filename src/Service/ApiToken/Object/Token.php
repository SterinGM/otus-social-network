<?php

namespace App\Service\ApiToken\Object;

readonly class Token
{
    public string $token;
    public string $userId;

    public function __construct(string $token, string $userId)
    {
        $this->token = $token;
        $this->userId = $userId;
    }
}