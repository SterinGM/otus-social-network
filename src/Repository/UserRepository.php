<?php

namespace App\Repository;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
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
        $statement->executeQuery();
    }

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
        $statement->executeQuery();
    }

    public function getById(string $id): ?User
    {
        $sql = 'SELECT * FROM user WHERE id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id);
        $result = $statement->executeQuery();

        return $this->mapUser($result);
    }

    private function mapUser(Result $result): ?User
    {
        if (!$result->rowCount()) {
            return null;
        }

        $data = $result->fetchAssociative();
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
}
