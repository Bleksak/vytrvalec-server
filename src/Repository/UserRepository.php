<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\UserCountByFacultyStatistics;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
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
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
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
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    /**
     * @return array<UserCountByFacultyStatistics>
     */
    public function countUserGroupedByFaculties(Season $season): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $rows = $queryBuilder
            ->select('f.id as id, count(u.id) as count')
            ->where($queryBuilder->expr()->exists(
                $this->getEntityManager()
                    ->createQueryBuilder()
                    ->select('1')
                    ->from(Submission::class, 's')
                    ->where('s.user = u')
                    ->andWhere('s.accepted = :accepted')
                    ->andWhere('s.season = :season')
                    ->getDQL()
            ))
            ->innerJoin('u.faculty', 'f')
            ->groupBy('f.id')
            ->orderBy('count', 'desc')
            ->setParameter('accepted', true)
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult();

        return array_map(
            fn (array $row): UserCountByFacultyStatistics => new UserCountByFacultyStatistics($row['id'], $row['count']),
            $rows,
        );
    }

    /**
     * @param array<int> $ids
     *
     * @return array<User>
     */
    public function findByIds(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $qb = $this->createQueryBuilder('u');

        $results = $qb
            ->select('u')
            ->where($qb->expr()->in('u.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        $orderMap = array_flip($ids);

        usort(
            $results,
            fn (User $a, User $b): int => $orderMap[$a->getId()] <=> $orderMap[$b->getId()]
        );

        return $results;
    }

    /**
     * @return array<User>
     */
    public function findAllForMailing(): array
    {
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
     * @return array<User>
     */
    public function findAllNotDeleted(): array
    {
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
