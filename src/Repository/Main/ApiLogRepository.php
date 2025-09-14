<?php

namespace App\Repository\Main;

use App\Entity\Main\ApiLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiLog>
 *
 * @method ApiLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiLog[]    findAll()
 * @method ApiLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiLogRepository extends ServiceEntityRepository
{
    private const string DATE_FORMAT = 'Y-m-d H:i:s';

    private Connection $connection;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiLog::class);

        $this->connection = $this->getEntityManager()->getConnection();
    }

    /**
     * @throws Exception
     */
    public function save(ApiLog $log): void
    {
        $sql = 'INSERT INTO api_log(id, user_id, uri, request, response, time, memory, created_at)
            VALUES(:id, :user_id, :uri, :request, :response, :time, :memory, :created_at)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $log->getId());
        $statement->bindValue('user_id', $log->getUserId());
        $statement->bindValue('uri', $log->getUri());
        $statement->bindValue('request', $log->getRequest());
        $statement->bindValue('response', $log->getResponse());
        $statement->bindValue('time', $log->getTime(), ParameterType::INTEGER);
        $statement->bindValue('memory', $log->getMemory(), ParameterType::INTEGER);
        $statement->bindValue('created_at', $log->getCreatedAt()->format(self::DATE_FORMAT));
        $statement->executeStatement();
    }
}
