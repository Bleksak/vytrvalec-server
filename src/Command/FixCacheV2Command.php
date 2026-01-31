<?php

declare(strict_types=1);

namespace App\Command;

use App\Dto\AnonymizedUser;
use App\Dto\SeasonResultDto;
use App\Entity\Faculty;
use App\Entity\Season;
use App\Entity\Submission;
use App\Entity\User;
use App\Repository\SeasonCacheRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:fix-cache-v2',
    description: 'Add a short description for your command',
)]
final readonly class FixCacheV2Command
{
    public function __construct(
        private SeasonCacheRepository $seasonCacheRepository,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private SubmissionRepository $submissionRepository,
    ) {}

    public function __invoke(): int
    {
        $caches = $this->seasonCacheRepository->findAll();

        foreach ($caches as $cache) {
            $newWeeklyResults = [];
            $data = $cache->data;
            $users = [];

            foreach ($data->results as $week) {
                $newActivityResults = [];

                foreach ($week->activities as $activity) {
                    $newFacultyResults = [];
                    foreach ($activity->results as $faculty) {
                        $newFacultyResults[$faculty->faculty] = $faculty;
                    }

                    $activity->results = $newFacultyResults;
                    foreach ($activity->extras as $extra) {
                        $userId = $this->findUserForAnonymizedUsers(
                            $cache->season,
                            $extra->faculty,
                            $extra->user,
                        );

                        $users[$userId] = $userId;
                        $extra->user = $userId;
                        $extra->activity = $activity->activity;
                    }
                    $newActivityResults[$activity->activity] = $activity;
                }

                $week->activities = $newActivityResults;
                $newWeeklyResults[$week->week] = $week;
            }

            $topThree = $this->submissionRepository->findOutliers($cache->season);

            $data->outliers = $topThree;

            foreach ($data->outliers as $outlier) {
                foreach ($outlier->results as $outlierResult) {
                    $users[$outlierResult->user] = $outlierResult->user;
                }
            }

            $cache->data = new SeasonResultDto(
                $newWeeklyResults,
                $data->outliers,
                \array_values($users),
            );

            $this->seasonCacheRepository->save($cache, false);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }

    private function findUserForAnonymizedUsers(
        Season $season,
        int $facultyId,
        int|AnonymizedUser $anonymizedUser,
    ): int {
        if (\is_int($anonymizedUser)) {
            return $anonymizedUser;
        }

        $facultyRef = $this->em->getReference(Faculty::class, $facultyId);

        $filter = [
            'firstName' => $anonymizedUser->firstName,
            'faculty' => $facultyRef,
        ];

        if ($anonymizedUser->lastName !== null) {
            $filter['lastName'] = $anonymizedUser->lastName;
        }

        $users = $this->userRepository->findBy($filter);

        $userids = [];

        foreach ($users as $user) {
            $userids[] = $user->id;
        }

        if (\count($userids) > 1) {
            $qb = $this->em->createQueryBuilder();

            /** @var list<int> */
            $usersWithSubmissions = $qb
                ->select('u.id')
                ->from(User::class, 'u')
                ->andWhere($qb->expr()->in('u.id', $userids))
                ->andWhere($qb->expr()->exists(
                    $this->em
                        ->createQueryBuilder()
                        ->select('1')
                        ->from(Submission::class, 'sub')
                        ->andWhere('sub.user = u.id')
                        ->andWhere('sub.season = :season'),
                ))
                ->setParameter('season', $season)
                ->getQuery()
                ->getSingleColumnResult();

            if (\count($usersWithSubmissions) > 1) {
                throw new \Exception(
                    'cannot continue, multiple users in the same season with same name and faculty',
                );
            }

            $userids = $usersWithSubmissions;
        }

        return $userids['0'] ?? 0;
    }
}
