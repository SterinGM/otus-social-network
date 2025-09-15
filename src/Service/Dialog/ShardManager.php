<?php

namespace App\Service\Dialog;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Uid\UuidV7;

class ShardManager
{
    public const string DIALOG_SHARD_MIGRATION_BOUNDARY = 'dialog_shard_migration_boundary';

    private Redis $redis;
    private EntityManagerInterface $em;
    private EntityManagerInterface $emShard0;
    private EntityManagerInterface $emShard1;

    public function __construct(
        string $host,
        EntityManagerInterface $em,
        EntityManagerInterface $emShard0,
        EntityManagerInterface $emShard1
    ) {
        /** @var Redis $redis */
        $redis = RedisAdapter::createConnection($host);
        $this->redis = $redis;

        $this->em = $em;
        $this->emShard0 = $emShard0;
        $this->emShard1 = $emShard1;
    }

    public function getEntityManagerForChat(string $chatId): EntityManagerInterface
    {
        $boundary = $this->getShardingBoundary();

        if (!$boundary || $chatId < $boundary) {
            // Старая стратегия
            $shardKey = $this->getShardKeyByChatId($chatId);
            $shard = $shardKey % 1; // 1 шард

            return match ($shard) {
                0 => $this->em,
                default => throw new InvalidArgumentException('Unknown old shard')
            };
        }

        // Новая стратегия
        $shardKey = $this->getShardKeyByChatId($chatId);
        $shard = $shardKey % 2; // 2 шарда

        return match ($shard) {
            0 => $this->emShard0,
            1 => $this->emShard1,
            default => throw new InvalidArgumentException('Unknown shard')
        };
    }

    private function getShardingBoundary(): ?string
    {
        $value = $this->redis->get(self::DIALOG_SHARD_MIGRATION_BOUNDARY);

        if ($value === false) {
            return null;
        }

        return (string)$value;
    }

    public function getShardKeyByChatId(string $chatId): string
    {
        return (string)(UuidV7::fromString($chatId)->getDateTime()->format('Uu') / 1000);
    }
}