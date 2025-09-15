<?php

namespace App\Command\Dialog;

use App\Service\Dialog\ShardManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:dialog:reshard:set-boundary',
    description: 'Установить начальный ID чата для миграции в новые шарды.',
)]
class DialogReshardingSetBoundaryCommand extends Command
{
    private ShardManager $shardManager;

    public function __construct(ShardManager $shardManager)
    {
        $this->shardManager = $shardManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $newMaxChatId = $this->shardManager->setShardingBoundary();

        $io->success("Граница миграции установлена: chat_id > $newMaxChatId");

        return Command::SUCCESS;
    }
}