<?php

namespace App\Service\ApiToken\Repository;

use App\Service\ApiToken\Object\Token;
use App\Service\ApiToken\TokenRepositoryInterface;
use App\Service\Exception\InvalidCredentialsException;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisTokenRepository implements TokenRepositoryInterface
{
    private const int TOKEN_TTL = 86400;

    private Redis $redis;

    public function __construct(string $host)
    {
        /** @var Redis $redis */
        $redis = RedisAdapter::createConnection($host);
        $this->redis = $redis;
    }

    public function save(Token $token): void
    {
        $lua = file_get_contents(__DIR__ . '/../../../../lua/auth/create_token.lua');
        $args = [
            $this->getTokenKey($token->token),
            $token->userId,
            $token->token,
            self::TOKEN_TTL,
        ];

        $this->redis->eval($lua, $args, 1);
    }

    public function getByToken(string $token): Token
    {
        $lua = file_get_contents(__DIR__ . '/../../../../lua/auth/get_token.lua');
        $args = [
            $this->getTokenKey($token),
        ];

        $userId = $this->redis->eval($lua, $args, 1);

        if (!$userId) {
            throw new InvalidCredentialsException();
        }

        return new Token(
            $token,
            $userId,
        );
    }

    private function getTokenKey(string $token): string
    {
        return 'auth_token:' . $token;
    }
}