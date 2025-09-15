<?php

namespace App\Repository\Dialog;

use App\Entity\Dialog\Chat;
use App\Service\Dialog\ShardManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 */
class ChatRepository extends ServiceEntityRepository
{
    private ShardManager $shardManager;

    public function __construct(ManagerRegistry $registry, ShardManager $shardManager)
    {
        parent::__construct($registry, Chat::class);

        $this->shardManager = $shardManager;
    }

    public function getChatById(string $chatId): ?Chat
    {
        $sql = 'SELECT * FROM chat WHERE id = :chatId';

        $em = $this->shardManager->getEntityManagerForChat($chatId);
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('chatId', $chatId);
        $result = $statement->executeQuery();

        if (!$result->rowCount()) {
            return null;
        }

        return $this->mapChat($result->fetchAssociative());
    }

    public function createChat(Chat $chat): void
    {
        $sql = 'INSERT INTO chat(id, user_ids) VALUES(:id, :user_ids)';

        $em = $this->shardManager->getEntityManagerForChat($chat->getId());
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('id', $chat->getId());
        $statement->bindValue('user_ids', json_encode($chat->getUserIds()));
        $statement->executeStatement();
    }

    private function mapChat(array $data): Chat
    {
        return new Chat()
            ->setId($data['id'])
            ->setUserIds(json_decode($data['user_ids']));
    }
}
