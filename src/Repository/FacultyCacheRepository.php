<?php

namespace App\Repository;

use App\Entity\FacultyCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FacultyCache>
 *
 * @method FacultyCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacultyCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacultyCache[]    findAll()
 * @method FacultyCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacultyCacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacultyCache::class);
    }

//    /**
//     * @return ActivityCache[] Returns an array of ActivityCache objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActivityCache
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
