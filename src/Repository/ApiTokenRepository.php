<?php

namespace App\Repository;

use App\Entity\ApiToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiToken>
 *
 * @method ApiToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiToken[]    findAll()
 * @method ApiToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    private Connection $connection;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);

        $this->connection = $this->getEntityManager()->getConnection();
    }

    public function create(ApiToken $token): void
    {
        $sql = 'INSERT INTO api_token(token, user_id, created_at)
            VALUES(:token, :user_id, :created_at)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('token', $token->getToken());
        $statement->bindValue('user_id', $token->getUserId());
        $statement->bindValue('created_at', $token->getCreatedAt()->format(self::DATE_FORMAT));
        $statement->executeQuery();
    }
}
