<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Faculty;
use App\Entity\FacultySummary;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Entity\UserSummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
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

    public function findAllByUser(User $user, int $page, int $limit)
    {

        return $this->findBy(['user' => $user], limit: $limit, offset: ($page - 1) * $limit);

//        $query = $this->getEntityManager()->createQueryBuilder()
//            ->select('partial s.{id, accepted, elevation, distance, reviewed, image, date}, season_fk')
//            ->from('App:Submission', 's')
//            ->innerJoin('s.season', 'season_fk')
//            ->innerJoin('s.activity', 'activity_fk')
//            ->where('s.user = :userId')
//            ->setFirstResult(($page-1) * $limit)
//            ->setMaxResults($limit)
//            ->setParameter('userId', $user->getId())
//            ->getQuery();
        // $qb->innerJoin('u.Group', 'g', 'WITH', 'u.status = ?1', 'g.id')

//        return $query->execute();
    }

    public function getAll(int $page, int $limit)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('s.id', 'season_fk.id as season', 'user_fk.id as user', 'activity_fk.id as activity', 's.accepted', 's.elevation', 's.distance', 's.reviewed', 's.image', 's.date')
            ->from('App:Submission', 's')
            ->innerJoin('s.season', 'season_fk')
            ->innerJoin('s.user', 'user_fk')
            ->innerJoin('s.activity', 'activity_fk')
            ->setFirstResult(($page-1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();
        // $qb->innerJoin('u.Group', 'g', 'WITH', 'u.status = ?1', 'g.id')

        return $query->execute();
    }

    public function findAcceptedInSeason(Season $season): array
    {
        return $this->findBy(['season' => $season, 'accepted' => true], orderBy: ['date' => 'ASC']);
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
