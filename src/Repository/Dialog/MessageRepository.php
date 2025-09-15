<?php

namespace App\Repository\Dialog;

use App\Entity\Dialog\Chat;
use App\Entity\Dialog\Message;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    private Connection $connection;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Message::class);

        $this->connection = $entityManager->getConnection();
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

    public function getMessages(Chat $chat)
    {
        $sql = 'SELECT * FROM message WHERE chat_id = :chat_id ORDER BY id DESC';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('chat_id', $chat->getId());
        $result = $statement->executeQuery();

        return $this->mapList($chat, $result->fetchAllAssociative());
    }

    private function mapMessage(Chat $chat, array $data): Message
    {
        return new Message()
            ->setId($data['id'])
            ->setChat($chat)
            ->setContent($data['content'])
            ->setUserId($data['user_id'])
            ->setCreatedAt(new DateTimeImmutable($data['created_at']));
    }

    /**
     * @return Message[]
     */
    private function mapList(Chat $chat, array $list): array
    {
        $result = [];

        foreach ($list as $data) {
            $post = $this->mapMessage($chat, $data);

            $result[$post->getId()] = $post;
        }

        return $result;
    }
}
