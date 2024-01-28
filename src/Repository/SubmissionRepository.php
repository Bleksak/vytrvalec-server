<?php

namespace App\Repository;

use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Submission>
 *
 * @method Submission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Submission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Submission[]    findAll()
 * @method Submission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Submission::class);
    }

    public function save(Submission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Submission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * @return Paginator<Submission>
     */
    public function findAllByUser(User $user, int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.user = :userId')
            ->addOrderBy('s.date', 'DESC')
            ->setParameter('userId', $user->getId());

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }
    /**
     * @return Paginator<Submission>
     */
    public function findBySeason(Season $season, int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.season = :seasonId')
            ->orderBy('s.date', 'DESC')
            ->setParameter('seasonId', $season->getId());

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
    * @return array<int,Submission>
    */
    public function findUnreviewed(int $limit): array
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.reviewed = :reviewed')
            ->setParameter('reviewed', false)
            ->orderBy('s.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getTotalStatistics(): array
    {
        $query = $this->getEntityManager()->getConnection()->prepare('
            SELECT a.name as activity, sub.distance as distance
            FROM (
                SELECT s.activity_id as activity_id, SUM(s.distance) as distance
                FROM submission s
                WHERE s.accepted = 1
                GROUP BY s.activity_id
            ) sub
            INNER JOIN activity a ON a.id = sub.activity_id;
        ');

        return $query->executeQuery()->fetchAllAssociative();
    }
}
