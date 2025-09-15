<?php

namespace App\Command\Dialog;

use App\Repository\Dialog\ChatRepository;
use App\Repository\Dialog\MessageRepository;
use App\Service\Dialog\ShardManager;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:dialog:reshard:migrate-shard',
    description: 'Перенести все чаты и сообщения из указанного старого шарда в новые по новой стратегии.',
)]
class DialogReshardingMigrateShardCommand extends Command
{
    protected static $defaultName = 'app:dialog:reshard:migrate-shard';

    private ShardManager $shardManager;
    private ChatRepository $chatRepository;
    private MessageRepository $messageRepository;

    public function __construct(
        ShardManager      $shardManager,
        ChatRepository    $chatRepository,
        MessageRepository $messageRepository
    ) {
        $this->shardManager = $shardManager;
        $this->chatRepository = $chatRepository;
        $this->messageRepository = $messageRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('old-shard', InputArgument::REQUIRED, 'Номер старого шарда для миграции');
        $this->addArgument('count-shards', InputArgument::REQUIRED, 'Количество новых шардов для миграции');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $oldShard = (int)$input->getArgument('old-shard');
        $countShards = (int)$input->getArgument('count-shards');

        if ($this->shardManager->isShardMigrated($oldShard)) {
            $io->success("Все чаты из старого шарда {$oldShard} уже успешно перенесены. Повторный запуск не требуется.");

            return Command::SUCCESS;
        }

        try {
            $oldEm = $this->shardManager->getOldShardEntityManager($oldShard);
        } catch (Exception) {
            $io->error('<error>Неверный номер старого шарда.</error>');

            return Command::FAILURE;
        }

        $chats = $this->chatRepository->getAllChats($oldEm);

        $total = count($chats);
        $batchSize = 100;
        $n = 0;

        foreach ($chats as $chat) {
            $shard = $this->shardManager->getShardByChatId($chat->getId(), $countShards);
            $newEm = $this->shardManager->getNewShardEntityManager($shard);

            try {
                $this->chatRepository->createChat($chat, $newEm);

                $messages = $this->messageRepository->getMessages($chat);
                foreach ($messages as $message) {
                    $this->messageRepository->createMessage($message, $newEm);
                }
            } catch (Exception) {
                $io->warning("Чат {$chat->getId()} уже мигрировал");
            }

            if ((++$n % $batchSize) === 0) {
                $io->info("Перенесено чатов: {$n}/{$total} (старый шард {$oldShard}, новый шард {$shard})");
            }
        }

        $this->shardManager->setShardMigrated($oldShard);

        $io->success("Все чаты из старого шарда {$oldShard} успешно перенесены.");

        return Command::SUCCESS;
    }
}