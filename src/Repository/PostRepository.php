<?php

namespace App\Repository;

use App\DTO\Post\Request\FeedRequest;
use App\Entity\Post;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
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

    /**
     * @throws Exception
     */
    public function getById(string $id): ?Post
    {
        $sql = 'SELECT * FROM post WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id);
        $result = $statement->executeQuery();

        if (!$result->rowCount()) {
            return null;
        }

        return $this->mapPost($result->fetchAssociative());
    }

    public function update(Post $post): void
    {
        $sql = 'UPDATE post SET text = :text WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $post->getId());
        $statement->bindValue('text', $post->getText());
        $statement->executeStatement();
    }

    public function delete(Post $post)
    {
        $sql = 'DELETE FROM post WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $post->getId());
        $statement->executeStatement();
    }

    public function getLastFriendsPosts(FeedRequest $feedRequest)
    {
        $sql = 'SELECT * FROM post WHERE author_id IN 
             (SELECT user_target FROM user_user WHERE user_source = :userId)
             ORDER BY id DESC LIMIT :limit OFFSET :offset';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('userId', $feedRequest->userId);
        $statement->bindValue('limit', $feedRequest->limit, ParameterType::INTEGER);
        $statement->bindValue('offset', $feedRequest->offset, ParameterType::INTEGER);
        $result = $statement->executeQuery();

        return $this->mapList($result->fetchAllAssociative());
    }

    private function mapPost(array $data): Post
    {
        $user = new User()
            ->setId($data['author_id']);

        return new Post()
            ->setId($data['id'])
            ->setText($data['text'])
            ->setAuthor($user)
            ->setCreatedAt(new DateTimeImmutable($data['created_at']));
    }

    /**
     * @return Post[]
     */
    private function mapList(array $list): array
    {
        $result = [];

        foreach ($list as $data) {
            $post = $this->mapPost($data);

            $result[$post->getId()] = $post;
        }

        return $result;
    }
}
