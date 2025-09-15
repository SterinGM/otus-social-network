<?php

namespace App\Repository\Dialog;

use App\Entity\Dialog\Chat;
use App\Entity\Dialog\Message;
use App\Service\Dialog\ShardManager;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    private ShardManager $shardManager;

    public function __construct(ManagerRegistry $registry, ShardManager $shardManager)
    {
        parent::__construct($registry, Message::class);

        $this->shardManager = $shardManager;
    }

    public function createMessage(Message $message, ?EntityManagerInterface $entityManager = null): void
    {
        $sql = 'INSERT INTO message(id, chat_id, content, user_id, created_at)
            VALUES(:id, :chat_id, :content, :user_id, :created_at)';

        $em = $entityManager ?? $this->shardManager->getEntityManagerForChat($message->getChat()->getId());
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('id', $message->getId());
        $statement->bindValue('chat_id', $message->getChat()->getId());
        $statement->bindValue('content', $message->getContent());
        $statement->bindValue('user_id', $message->getUserId());
        $statement->bindValue('created_at', $message->getCreatedAt()->format(self::DATE_FORMAT));
        $statement->executeStatement();
    }

    /**
     * @return Message[]
     */
    public function getAllMessages(Chat $chat): array
    {
        $sql = 'SELECT * FROM message WHERE chat_id = :chat_id ORDER BY id DESC';

        $em = $this->shardManager->getEntityManagerForChat($chat->getId());
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('chat_id', $chat->getId());
        $result = $statement->executeQuery();

        return $this->mapList($chat, $result->fetchAllAssociative());
    }

    /**
     * @return Message[]
     */
    public function getMessagesFromId(Chat $chat, string $fromMessageId, ?EntityManagerInterface $entityManager = null): array
    {
        $sql = 'SELECT * FROM message WHERE chat_id = :chat_id AND id >= :message_id ORDER BY id DESC';

        $em = $entityManager ?? $this->shardManager->getEntityManagerForChat($chat->getId());
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('chat_id', $chat->getId());
        $statement->bindValue('message_id', $fromMessageId);
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
            $message = $this->mapMessage($chat, $data);

            $result[$message->getId()] = $message;
        }

        return $result;
    }
}
