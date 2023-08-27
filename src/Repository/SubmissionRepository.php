<?php

namespace App\Repository;

use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findAllByUser(User $user, int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.user = :userId')
            ->setParameter('userId', $user->getId())
        ;

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page-1) * $limit)
            ->setMaxResults($limit)
        ;

        return $paginator;
    }

    public function findBySeason(Season $season, int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.season = :seasonId')
            ->setParameter('seasonId', $season->getId())
        ;

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page-1) * $limit)
            ->setMaxResults($limit)
        ;

        return $paginator;
    }

    public function findUsersBySeason(Season $season, int $page, int $limit): Paginator
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->join('u.submissions', 's')
            ->where('s.season = :seasonId')

            ->setParameter('seasonId', $season->getId())
            ->distinct()
        ;

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page-1) * $limit)
            ->setMaxResults($limit)
        ;

        return $paginator;
    }

    public function findAcceptedInSeasonAndWeek(Season $season, int $week): array
    {
        // TODO: re-enable this
//        $maxWeek = intdiv((new \DateTimeImmutable())->diff($season->getStart())->days, 7);
//        if($week > $maxWeek) {
//            return [];
//        }

        return $this->findBy(['season' => $season, 'accepted' => true, 'week' => $week], orderBy: ['date' => 'ASC']);
    }

//    /**
//     * @return Submission[] Returns an array of Submission objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Submission
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
