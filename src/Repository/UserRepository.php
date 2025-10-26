<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\UserCountByFacultyStatistics;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(mixed[] $criteria, mixed[] $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(mixed[] $criteria, mixed[] $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository implements
    PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[\Override]
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        #[SensitiveParameter] string $newHashedPassword,
    ): void {
        \assert(
            $user instanceof User,
            'unreachable: user is instance of ' . $user::class,
        );

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    /**
     * @return list<UserCountByFacultyStatistics>
     */
    public function countUserGroupedByFaculties(Season $season): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        /** @var list<array{id: integer, count: integer}> */
        $rows = $queryBuilder
            ->select('f.id as id, count(u.id) as count')
            ->where(
                $queryBuilder
                    ->expr()
                    ->exists(
                        $this->getEntityManager()
                            ->createQueryBuilder()
                            ->select('1')
                            ->from(Submission::class, 's')
                            ->where('s.user = u')
                            ->andWhere('s.accepted = :accepted')
                            ->andWhere('s.season = :season')
                            ->getDQL(),
                    ),
            )
            ->innerJoin('u.faculty', 'f')
            ->groupBy('f.id')
            ->orderBy('count', 'desc')
            ->setParameter('accepted', true)
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult();

        return \array_map(
            static fn(array $row): UserCountByFacultyStatistics => new UserCountByFacultyStatistics(
                $row['id'],
                $row['count'],
            ),
            $rows,
        );
    }

    /**
     * @return list<User>
     */
    public function findAllForMailing(): array
    {
        /** @var list<User> */
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.mailing = 1')
            ->getQuery()
            ->getResult();
    }

    public function findByUnsubscribeHash(string $unsubscribeHash): ?User
    {
        return $this->findOneBy(['emailUnsubscribeHash' => $unsubscribeHash]);
    }

    /**
     * @return list<User>
     */
    public function findAllNotDeleted(): array
    {
        /** @var list<User> */
        return $this->createQueryBuilder('u')
            ->where('u.email IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }
}
