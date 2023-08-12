<?php

namespace App\Repository;

use App\Entity\ProfileCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProfileCache>
 *
 * @method ProfileCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileCache[]    findAll()
 * @method ProfileCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileCacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileCache::class);
    }

//    /**
//     * @return ProfileCache[] Returns an array of ProfileCache objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProfileCache
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
