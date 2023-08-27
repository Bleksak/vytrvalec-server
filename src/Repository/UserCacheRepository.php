<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\ProfileCache;
use App\Entity\Season;
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
            'season' => $submission->getSeason(),
        ]) ?? new UserCache($submission->getUser(), $submission->getActivity(), $submission->getSeason(), $submission->getWeek());

        $userCache
            ->updateDistance(fn($oldDistance) => $oldDistance + $submission->getDistance())
            ->updateElevation(fn($oldElevation) => $oldElevation + $submission->getElevation())
        ;

        $this->save($userCache, $flush);
    }

    public function findMaxDistanceBySeasonAndWeek(Season $season, int $week, Activity $activity): array
    {
        $query = $this->createQueryBuilder('uc')
            ->select('max(uc.distance) as distance', 'IDENTITY(uc.user) as user', 'IDENTITY(uc.activity) as activity')
            ->where('uc.season = :season')
            ->andWhere('uc.week = :week')
            ->andWhere('uc.activity = :activity')
            ->groupBy('uc.user')

            ->setParameter('season', $season)
            ->setParameter('week', $week)
            ->setParameter('activity', $activity)
            ->getQuery()
        ;

        return $query->execute();
//        return $this->findBy(['season' => $season, 'week' => $week, 'activity' => $activity]);
    }

    public function findMaxElevationBySeasonAndWeek(Season $season, int $week, Activity $activity): array
    {
        $query = $this->createQueryBuilder('uc')
            ->select('max(uc.elevation) as elevation', 'IDENTITY(uc.user) as user', 'IDENTITY(uc.activity) as activity')
            ->where('uc.season = :season')
            ->andWhere('uc.week = :week')
            ->andWhere('uc.activity = :activity')
            ->groupBy('uc.user')

            ->setParameter('season', $season)
            ->setParameter('week', $week)
            ->setParameter('activity', $activity)
            ->getQuery()
        ;

        return $query->execute();
//        return $this->findBy(['season' => $season, 'week' => $week, 'activity' => $activity]);
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
