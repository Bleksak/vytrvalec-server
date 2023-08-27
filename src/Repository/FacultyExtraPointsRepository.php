<?php

namespace App\Repository;

use App\CustomLogic\PointCalculator;
use App\Entity\Activity;
use App\Entity\Faculty;
use App\Entity\FacultyCache;
use App\Entity\FacultyExtraPoints;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Entity\UserCache;
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
    public function __construct(
        ManagerRegistry $registry,
        private readonly PointCalculator $pointCalculator,
        private readonly ExtraPointsRepository $extraPointsRepository,
        private readonly FacultyCacheRepository $facultyCacheRepository,
    )
    {
        parent::__construct($registry, FacultyExtraPoints::class);
    }

    public function constructForWeek(Season $season, int $week): void
    {
        $facultyCache = $this->facultyCacheRepository->findByWeek($season, $week);

        foreach($facultyCache as $cache) {
            foreach($this->findBy(['cache' => $cache]) as $extraPointsCache) {
                $this->getEntityManager()->remove($extraPointsCache);
            }
        }

        $points = $this->pointCalculator->processWeek($season, $week);

        foreach($points as $activityId => $results) {
            foreach($results['extras'] as $extra) {
                $extraPoints = $this->extraPointsRepository->findByName($extra['name']);
                $activityRef = $this->getEntityManager()->getReference(Activity::class, $activityId);

                foreach($extra['users'] as $user => $faculty) {
                    $userRef = $this->getEntityManager()->getReference(User::class, $user);
                    $facultyRef = $this->getEntityManager()->getReference(Faculty::class, $faculty);

                    $cache = $this->facultyCacheRepository->findCache($facultyRef, $activityRef, $week, $season);
                    if($cache === null) {
                        continue;
                    }

                    $facultyExtraPoints = (new FacultyExtraPoints())
                        ->setUser($userRef)
                        ->setCache($cache)
                        ->setExtraPoints($extraPoints)
                        ->setValue($extra['value'])
                    ;

                    $this->getEntityManager()->persist($facultyExtraPoints);
                }
            }
        }

        $this->getEntityManager()->flush();
    }
}
