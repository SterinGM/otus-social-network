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
        $lastToken = $this->getLastUserToken($token->userId);

        if ($lastToken) {
            $this->deleteToken($lastToken);
        }

        $this->saveUserToken($token);
        $this->saveLastUserToken($token);
    }

    public function getByToken(string $token): Token
    {
        $userId = $this->getUserIdByToken($token);

        if (!$userId) {
            throw new InvalidCredentialsException();
        }

        $lastToken = $this->getLastUserToken($userId);

        if (!$lastToken) {
            throw new InvalidCredentialsException();
        }

        if ($lastToken !== $token) {
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

    private function getLastTokenKey(string $userId): string
    {
        return 'auth_last_token:' . $userId;
    }

    private function saveUserToken(Token $token): void
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

    private function saveLastUserToken(Token $token): void
    {
        $lua = file_get_contents(__DIR__ . '/../../../../lua/auth/set_last_user_token.lua');
        $args = [
            $this->getLastTokenKey($token->userId),
            $token->token,
            self::TOKEN_TTL,
        ];

        $this->redis->eval($lua, $args, 1);
    }

    private function getUserIdByToken(string $token): string|bool
    {
        $lua = file_get_contents(__DIR__ . '/../../../../lua/auth/get_token.lua');
        $args = [
            $this->getTokenKey($token),
        ];

        return $this->redis->eval($lua, $args, 1);
    }

    private function getLastUserToken(string $userId): string|bool
    {
        $lua = file_get_contents(__DIR__ . '/../../../../lua/auth/get_last_user_token.lua');
        $args = [
            $this->getLastTokenKey($userId),
        ];

        return $this->redis->eval($lua, $args, 1);
    }

    private function deleteToken(bool|string $lastToken): void
    {
        $lua = file_get_contents(__DIR__ . '/../../../../lua/auth/delete_token.lua');
        $args = [
            $this->getTokenKey($lastToken),
        ];

        $this->redis->eval($lua, $args, 1);
    }
}