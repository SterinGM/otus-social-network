<?php

namespace App\Command\Dialog;

use App\Service\Dialog\ShardManager;
use Redis;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:dialog:reshard:set-boundary',
    description: 'Установить начальный ID чата для миграции в новые шарды.',
)]
class DialogReshardingSetBoundaryCommand extends Command
{
    protected static $defaultName = 'app:dialog:reshard:set-boundary';

    private Redis $redis;

    public function __construct(string $host)
    {
        /** @var Redis $redis */
        $redis = RedisAdapter::createConnection($host);
        $this->redis = $redis;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newMaxChatId = Uuid::v7()->toRfc4122();

        $this->redis->set(ShardManager::DIALOG_SHARD_MIGRATION_BOUNDARY, Uuid::v7()->toRfc4122());

        $output->writeln("Граница миграции установлена: chat_id > $newMaxChatId");

        return Command::SUCCESS;
    }
}