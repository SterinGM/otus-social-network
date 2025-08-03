<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    private Connection $connection;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);

        $this->connection = $this->getEntityManager()->getConnection();
    }
    /**
     * @throws Exception
     */
    public function create(Post $post): void
    {
        $sql = 'INSERT INTO post(id, author_id, text, created_at)
            VALUES(:id, :author_id, :text, :created_at)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $post->getId());
        $statement->bindValue('author_id', $post->getAuthor()->getId());
        $statement->bindValue('text', $post->getText());
        $statement->bindValue('created_at', $post->getCreatedAt()->format(self::DATE_FORMAT));
        $statement->executeStatement();
    }
}
