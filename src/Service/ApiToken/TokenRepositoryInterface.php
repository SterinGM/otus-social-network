<?php

namespace App\Service\ApiToken;

use App\Service\ApiToken\Object\Token;

interface TokenRepositoryInterface
{
    public function save(Token $token): void;

    public function getByToken(string $token): Token;
}