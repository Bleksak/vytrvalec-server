<?php

namespace App\Repository;

use App\Entity\FacultyExtraPoints;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FacultyExtraPoints>
 *
 * @method FacultyExtraPoints|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacultyExtraPoints|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacultyExtraPoints[]    findAll()
 * @method FacultyExtraPoints[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacultyExtraPointsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacultyExtraPoints::class);
    }

    public function getOrConstruct()
    {
        
    }

//    /**
//     * @return FacultyExtraPoints[] Returns an array of FacultyExtraPoints objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FacultyExtraPoints
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
