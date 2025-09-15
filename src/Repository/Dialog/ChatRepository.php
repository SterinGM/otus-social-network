<?php

namespace App\Repository\Dialog;

use App\Entity\Dialog\Chat;
use App\Service\Dialog\ShardManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function createChat(Chat $chat, ?EntityManagerInterface $entityManager = null): void
    {
        $sql = 'INSERT INTO chat(id, user_ids) VALUES(:id, :user_ids)';

        $em = $entityManager ?? $this->shardManager->getEntityManagerForChat($chat->getId());
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('id', $chat->getId());
        $statement->bindValue('user_ids', json_encode($chat->getUserIds()));
        $statement->executeStatement();
    }

    /**
     * @return Chat[]
     */
    public function getAllChats(EntityManagerInterface $em): array
    {
        $sql = 'SELECT * FROM chat';

        $statement = $em->getConnection()->prepare($sql);
        $result = $statement->executeQuery();

        return $this->mapChatList($result->fetchAllAssociative());
    }

    private function mapChat(array $data): Chat
    {
        return new Chat()
            ->setId($data['id'])
            ->setUserIds(json_decode($data['user_ids']));
    }

    /**
     * @return Chat[]
     */
    private function mapChatList(array $list): array
    {
        $result = [];

        foreach ($list as $data) {
            $chat = $this->mapChat($data);

            $result[$chat->getId()] = $chat;
        }

        return $result;
    }
}
