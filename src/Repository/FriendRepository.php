<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class FriendRepository
{
    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->connection = $entityManager->getConnection();
    }

    /**
     * @throws Exception
     */
    public function setFriend(string $userSource, string $userTarget): void
    {
        $sql = 'INSERT INTO user_user(user_source, user_target) VALUES(:user_source, :user_target)
            ON DUPLICATE KEY UPDATE user_target = user_target';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('user_source', $userSource);
        $statement->bindValue('user_target', $userTarget);
        $statement->executeStatement();
    }

    /**
     * @throws Exception
     */
    public function deleteFriend(string $userSource, string $userTarget)
    {
        $sql = 'DELETE FROM user_user WHERE user_source = :user_source AND user_target = :user_target';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('user_source', $userSource);
        $statement->bindValue('user_target', $userTarget);
        $statement->executeStatement();
    }
}
