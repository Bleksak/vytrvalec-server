<?php

namespace App\Repository;

use App\Entity\ExtraPoints;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExtraPoints>
 *
 * @method ExtraPoints|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtraPoints|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExtraPoints[]    findAll()
 * @method ExtraPoints[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtraPointsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExtraPoints::class);
    }

//    /**
//     * @return ExtraPoints[] Returns an array of ExtraPoints objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ExtraPoints
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
