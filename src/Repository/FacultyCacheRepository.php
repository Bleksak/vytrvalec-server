<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Faculty;
use App\Entity\FacultyCache;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\UserCache;
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

    public function save(FacultyCache $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function addCache(Submission $submission, bool $flush = false): void
    {
        $facultyCache = $this->findOneBy([
            'faculty' => $submission->getUser()->getFaculty(),
            'activity' => $submission->getActivity(),
            'week' => $submission->getWeek(),
            'season' => $submission->getSeason(),
        ]) ?? new FacultyCache($submission->getUser()->getFaculty(), $submission->getActivity(), $submission->getSeason(), $submission->getWeek());

        $facultyCache
            ->updateDistance(fn($oldDistance) => $oldDistance + $submission->getDistance())
            ->updateElevation(fn($oldElevation) => $oldElevation + $submission->getElevation())
        ;

        $this->save($facultyCache, $flush);
    }

    public function findByWeek(Season $season, int $week): array
    {
        return $this->findBy(['season' => $season, 'week' => $week]);
    }

    public function findCaches(Season $season): array
    {
        return $this->findBy(['season' => $season]);
    }

    public function findCache(Faculty $faculty, Activity $activity, int $week, Season $season): ?FacultyCache
    {
        return $this->findOneBy([
            'faculty' => $faculty,
            'activity' => $activity,
            'week' => $week,
            'season' => $season,
        ]);
    }
}
