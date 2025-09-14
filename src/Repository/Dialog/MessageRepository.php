<?php

namespace App\Repository\Dialog;

use App\Entity\Dialog\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    private Connection $connection;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);

        $this->connection = $this->getEntityManager()->getConnection();
    }

    public function createMessage(Message $message): void
    {
        $sql = 'INSERT INTO message(id, chat_id, content, user_id, created_at)
            VALUES(:id, :chat_id, :content, :user_id, :created_at)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $message->getId());
        $statement->bindValue('chat_id', $message->getChat()->getId());
        $statement->bindValue('content', $message->getContent());
        $statement->bindValue('user_id', $message->getUserId());
        $statement->bindValue('created_at', $message->getCreatedAt()->format(self::DATE_FORMAT));
        $statement->executeStatement();
    }
}
