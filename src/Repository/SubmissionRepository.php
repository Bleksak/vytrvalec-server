<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findAllByUser(User $user, int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.user = :userId')
            ->orderBy('s.date DESC')
            ->setParameter('userId', $user->getId());

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }

    public function findBySeason(Season $season, int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.season = :seasonId')
            ->orderBy('s.date DESC')
            ->setParameter('seasonId', $season->getId());

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }

    public function findUsersBySeason(Season $season, int $page, int $limit): Paginator
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->join('u.submissions', 's')
            ->where('s.season = :seasonId')

            ->setParameter('seasonId', $season->getId())
            ->distinct();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @return array<int, Submission>
     */
    public function findAcceptedInSeasonAndWeek(Season $season, int $week): array
    {
        // TODO: re-enable this
        // $maxWeek = intdiv((new \DateTimeImmutable())->diff($season->getStart())->days, 7);
        // if($week > $maxWeek) {
        //     return [];
        // }

        return $this->createQueryBuilder('sub')
            ->select('sub')
            ->where('sub.season = :season')
            ->andWhere('sub.week = :week')
            ->andWhere('sub.accepted = :accepted')
            ->orderBy('sub.activity_id', 'ASC')
            ->orderBy('user.faculty_id', 'ASC')
            ->orderBy('sub.date', 'ASC')
            ->getQuery()
            ->setParameters([
                'week' => $week,
                'season' => $season,
                'accepted' => true
            ])
            ->setFetchMode(Submission::class, 'submission', ClassMetadataInfo::FETCH_EAGER)
            ->setFetchMode(Submission::class, 'user', ClassMetadataInfo::FETCH_EAGER)
            ->setFetchMode(Submission::class, 'activity', ClassMetadataInfo::FETCH_EAGER)
            ->setFetchMode(User::class, 'faculty', ClassMetadataInfo::FETCH_EAGER)
            ->execute();
    }

    public function findUnreviewedInSeason(Season $season): array
    {
        return $this->findBy(['season' => $season, 'reviewed' => false]);
    }

    private function getDailyMax(array $submissions, $day, $startIndex): array
    {
        $index = $startIndex;
        $distance = [];
        $increment = 1;

        while ($index < sizeof($submissions)) {
            if ($submissions[$index]->getDate()->diff($day)->days != 0) {
                $increment = 0;
                break;
            }

            $submission = $submissions[$index];
            if (!array_key_exists($submission->getUser()->getId(), $distance)) {
                $distance[$submission->getUser()->getId()] = 0;
            }

            $distance[$submission->getUser()->getId()] += $submission->getDistance();

            $index += 1;
        }

        $users = [];
        $maxDistance = 0;

        foreach ($distance as $user => $value) {
            if ($value === $maxDistance) {
                $users[] = $user;
            }

            if ($value > $maxDistance) {
                $maxDistance = $value;
                $users = [$user];
            }
        }

        $nextIndex = $index + $increment < sizeof($submissions) ? $index + $increment : null;

        return [
            'next' => $nextIndex,
            'distance' => $maxDistance,
            'users' => $users
        ];
    }

    public function findMaxDailyDistanceBySeasonAndWeek(Season $season, int $week, Activity $activity): array
    {
        /**
         * @var Submission[] $submissions
         */
        $submissions = $this->findBy(['season' => $season, 'week' => $week, 'activity' => $activity, 'accepted' => true], orderBy: ['date' => 'ASC']);

        if (empty($submissions)) {
            return [];
        }

        $index = 0;

        $currentMax = 0;
        $currentUsers = [];

        while ($index !== null) {
            $day = $submissions[$index]?->getDate();
            $result = $this->getDailyMax($submissions, $day, $index);

            $index = $result['next'];
            $distance = $result['distance'];
            $users = $result['users'];

            if ($distance === $currentMax) {
                $currentUsers = array_merge($currentUsers, $users);
            }

            if ($distance > $currentMax) {
                $currentMax = $distance;
                $currentUsers = $users;
            }
        }

        return array_map(fn ($user) => [
            'user' => $user,
            'distance' => $currentMax,
            'activity' => $activity->getId()
        ], $currentUsers);
    }
}
