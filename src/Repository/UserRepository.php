<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\Statistics\UserCountGroupedByFacultyTotal;
use App\Dto\UserCountByFacultyStatistics;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends AbstractRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(mixed[] $criteria, array<string, string('ASC')|string('DESC')|string('asc')|string('desc')>|null $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends AbstractRepository implements
    PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    #[\Override]
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        #[SensitiveParameter]
        string $newHashedPassword,
    ): void {
        \assert(
            $user instanceof User,
            'unreachable: user is instance of ' . $user::class,
        );

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    public function countUserGroupedByFaculties(Season $season): UserCountGroupedByFacultyTotal
    {
        $queryBuilder = $this->createQueryBuilder('u');

        /** @var list<array{id: integer, count: integer}> */
        $rows = $queryBuilder
            ->select('f.id as id, count(u.id) as count')
            ->where(
                $queryBuilder
                    ->expr()
                    ->exists(
                        $this
                            ->getEntityManager()
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

        $total = 0;
        $users = [];

        foreach ($rows as $row) {
            $users[] = new UserCountByFacultyStatistics(
                $row['id'],
                $row['count'],
            );

            $total += $row['count'];
        }

        return new UserCountGroupedByFacultyTotal($users, $total);
    }

    /**
     * @return list<User>
     */
    public function findAllForMailing(): array
    {
        /** @var list<User> */
        return $this
            ->createQueryBuilder('u')
            ->select('u')
            ->where('u.mailing = 1')
            ->getQuery()
            ->getResult();
    }

    public function findByUnsubscribeHash(string $unsubscribeHash): ?User
    {
        return $this->findOneBy(['emailUnsubscribeHash' => $unsubscribeHash]);
    }

    public function findByPasswordResetToken(
        #[SensitiveParameter]
        string $passwordResetToken,
    ): ?User {
        return $this->findOneBy(['passwordResetToken' => $passwordResetToken]);
    }

    /**
     * @return list<User>
     */
    public function findAllNotDeleted(): array
    {
        /** @var list<User> */
        return $this
            ->createQueryBuilder('u')
            ->where('u.email IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Paginator<User>
     */
    public function findAllNotDeletedPaginated(
        int $page,
        int $limit,
        string $search = '',
    ): Paginator {
        $qb = $this
            ->createQueryBuilder('u')
            ->leftJoin('u.faculty', 'f')
            ->where('u.email IS NOT NULL')
            ->orderBy('u.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        if ($search !== '') {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(u.firstName)', ':search'),
                $qb->expr()->like('LOWER(u.lastName)', ':search'),
                $qb->expr()->like('LOWER(u.email)', ':search'),
                $qb->expr()->like('LOWER(f.shortcut)', ':search'),
            ))->setParameter('search', '%' . \mb_strtolower($search) . '%');
        }

        /** @var Paginator<User> */
        return new Paginator($qb->getQuery());
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * @param list<int> $ids
     * @return list<User>
     */
    public function findByIds(array $ids): array
    {
        /** @var list<User> */
        return $this
            ->createQueryBuilder('u', 'u.id')
            ->select('u')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
