<?php

namespace App\Repository\Main;

use App\Entity\Main\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public const string BIRTHDATE_FORMAT = 'Y-m-d';

    private Connection $connection;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);

        $this->connection = $this->getEntityManager()->getConnection();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @throws Exception
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $sql = 'UPDATE user SET password = :password WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('password', $newHashedPassword);
        $statement->bindValue('id', $user->getId());
        $statement->executeStatement();
    }

    /**
     * @throws Exception
     */
    public function createUser(User $user): void
    {
        $sql = 'INSERT INTO user(id, password, roles, first_name, second_name, birthdate, biography, city)
            VALUES(:id, :password, :roles, :firstName, :secondName, :birthdate, :biography, :city)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $user->getId());
        $statement->bindValue('password', $user->getPassword());
        $statement->bindValue('roles', json_encode($user->getRoles()));
        $statement->bindValue('firstName', $user->getFirstName());
        $statement->bindValue('secondName', $user->getSecondName());
        $statement->bindValue('birthdate', $user->getBirthdate()->format(self::BIRTHDATE_FORMAT));
        $statement->bindValue('biography', $user->getBiography());
        $statement->bindValue('city', $user->getCity());
        $statement->executeStatement();
    }

    /**
     * @param User[] $users
     * @throws Exception
     */
    public function createUsers(array $users): void
    {
        $sql = 'INSERT INTO user(id, password, roles, first_name, second_name, birthdate, biography, city) VALUES';

        foreach ($users as $n => $user) {
            $sql .= ($n ? ',' : '') . "(:id$n, :password$n, :roles$n, :firstName$n, :secondName$n, :birthdate$n, :biography$n, :city$n)";
        }

        $statement = $this->connection->prepare($sql);

        foreach ($users as $n => $user) {
            $statement->bindValue('id' . $n, $user->getId());
            $statement->bindValue('password' . $n, $user->getPassword());
            $statement->bindValue('roles' . $n, json_encode($user->getRoles()));
            $statement->bindValue('firstName' . $n, $user->getFirstName());
            $statement->bindValue('secondName' . $n, $user->getSecondName());
            $statement->bindValue('birthdate' . $n, $user->getBirthdate()->format(self::BIRTHDATE_FORMAT));
            $statement->bindValue('biography' . $n, $user->getBiography());
            $statement->bindValue('city' . $n, $user->getCity());
        }

        $statement->executeStatement();
    }

    /**
     * @throws Exception
     */
    public function getById(string $id): ?User
    {
        $sql = 'SELECT * FROM user WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id);
        $result = $statement->executeQuery();

        if (!$result->rowCount()) {
            return null;
        }

        return $this->mapUser($result->fetchAssociative());
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function getIdList(int $limit = 100): array
    {
        $sql = 'SELECT id FROM user ORDER BY id ASC LIMIT :limit';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('limit', $limit, ParameterType::INTEGER);
        $result = $statement->executeQuery();

        return $result->fetchFirstColumn();
    }

    /**
     * @return User[]
     * @throws Exception
     */
    public function search(string $firstName, string $lastName, int $limit = 100): array
    {
        $sql = 'SELECT * FROM user WHERE id IN 
            (SELECT id FROM user WHERE first_name LIKE :firstName AND second_name LIKE :secondName)
            ORDER BY id ASC LIMIT :limit';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('firstName', $firstName . '%');
        $statement->bindValue('secondName', $lastName . '%');
        $statement->bindValue('limit', $limit, ParameterType::INTEGER);
        $result = $statement->executeQuery();

        return $this->mapList($result->fetchAllAssociative());
    }

    private function mapUser(array $data): User
    {
        $birthdate = DateTimeImmutable::createFromFormat(UserRepository::BIRTHDATE_FORMAT, $data['birthdate']);

        return new User()
            ->setId($data['id'])
            ->setFirstName($data['first_name'])
            ->setSecondName($data['second_name'])
            ->setBirthdate($birthdate)
            ->setBiography($data['biography'])
            ->setCity($data['city'])
            ->setPassword($data['password']);
    }

    /**
     * @return User[]
     */
    private function mapList(array $list): array
    {
        $result = [];

        foreach ($list as $data) {
            $user = $this->mapUser($data);

            $result[$user->getId()] = $user;
        }

        return $result;
    }
}
