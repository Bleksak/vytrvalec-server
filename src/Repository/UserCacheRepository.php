<?php

namespace App\Repository;

use App\Entity\ProfileCache;
use App\Entity\Submission;
use App\Entity\UserCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCache>
 *
 * @method UserCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCache[]    findAll()
 * @method UserCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCache::class);
    }

    public function save(UserCache $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addCache(Submission $submission, bool $flush = false): void
    {
        $userCache = $this->findOneBy([
            'user' => $submission->getUser(),
            'activity' => $submission->getActivity(),
            'week' => $submission->getWeek(),
        ]) ?? new UserCache($submission->getUser(), $submission->getActivity(), $submission->getWeek());

        $userCache
            ->updateDistance(fn($oldDistance) => $oldDistance + $submission->getDistance())
            ->updateElevation(fn($oldElevation) => $oldElevation + $submission->getElevation())
        ;

        $this->save($userCache, $flush);
    }

//    /**
//     * @return UserCache[] Returns an array of UserCache objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserCache
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
