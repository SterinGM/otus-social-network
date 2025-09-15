<?php

namespace App\Command\Dialog;

use App\Service\Dialog\ShardManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:dialog:reshard:finish',
    description: 'Удалить границу решардинга. Теперь всё пишется и читается из новых шардов.',
)]
class DialogReshardingFinishCommand extends Command
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
        $countShards = ShardManager::DIALOG_OLD_SHARDS_COUNT;

        $this->shardManager->clearShardingCache($countShards);

        $io->success("Граница миграции успешно удалена");

        return Command::SUCCESS;
    }
}