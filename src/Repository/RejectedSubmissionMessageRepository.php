<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Faculty;
use App\Entity\RejectedSubmissionMessage;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RejectedSubmissionMessage>
 *
 * @method RejectedSubmissionMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method RejectedSubmissionMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method RejectedSubmissionMessage[]    findAll()
 * @method RejectedSubmissionMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RejectedSubmissionMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RejectedSubmissionMessage::class);
    }

    public function save(RejectedSubmissionMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RejectedSubmissionMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUser(User $user): array
    {
        $query = $this->createQueryBuilder('m')
            ->select('m.message, s.id, s.accepted, s.reviewed, s.elevation, s.distance, s.image, s.date, a.name as activity')
            ->join(Submission::class, 's')
            ->join(Activity::class, 'a', Join::WITH, 'a.id = s.activity')
            ->where('s.user = :user')

            ->setParameter('user', $user)
            ->getQuery()
        ;

        return $query->execute();
    }
//    /**
//     * @return RejectedSubmissionMessage[] Returns an array of RejectedSubmissionMessage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RejectedSubmissionMessage
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
