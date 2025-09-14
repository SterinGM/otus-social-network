<?php

namespace App\Repository\Dialog;

use App\Entity\Dialog\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 */
class ChatRepository extends ServiceEntityRepository
{
    private Connection $connection;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);

        $this->connection = $this->getEntityManager()->getConnection();
    }

    public function getChatByUsers(string $userId1, string $userId2): ?Chat
    {
        $userIds = array_unique([$userId1, $userId2]);
        sort($userIds);

        $sql = 'SELECT * FROM chat WHERE user_ids = :user_ids';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('user_ids', json_encode($userIds));
        $result = $statement->executeQuery();

        if (!$result->rowCount()) {
            return null;
        }

        return $this->mapChat($result->fetchAssociative());
    }

    public function createChat(Chat $chat): void
    {
        $userIds = array_unique($chat->getUserIds());
        sort($userIds);

        $sql = 'INSERT INTO chat(id, user_ids) VALUES(:id, :user_ids)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $chat->getId());
        $statement->bindValue('user_ids', json_encode($userIds));
        $statement->executeStatement();
    }

    private function mapChat(array $data): Chat
    {
        return new Chat()
            ->setId($data['id'])
            ->setUserIds(json_decode($data['user_ids']));
    }
}
