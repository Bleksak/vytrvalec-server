<?php

declare(strict_types=1);

namespace App\Services;

use App\CustomLogic\SeasonResultCalculator;
use App\Dto\AnonymizedUser;
use App\Dto\ExtraPointsDto;
use App\Dto\FacultyResultDto;
use App\Dto\SeasonResult\SeasonResultRankDto;
use App\Dto\SeasonResult\SeasonResultRankRowDto;
use App\Dto\SeasonResultWithUsersDto;
use App\Dto\WeeklyResultDto;
use App\Entity\Season;
use App\Entity\User;
use App\Repository\SeasonCacheRepository;
use App\Repository\UserRepository;

final readonly class SeasonResultRankingService
{
    public function __construct(
        private SeasonCacheRepository $seasonCacheRepository,
        private SeasonResultCalculator $seasonResultCalculator,
        private UserRepository $userRepository,
    ) {}

    public function getSeasonResult(Season $season): SeasonResultWithUsersDto
    {
        $cache = $this->seasonCacheRepository->findOneBy([
            'season' => $season->id,
        ]);

        $data = $cache?->data;

        if ($data === null) {
            $data = $this->seasonResultCalculator->calculate($season);
        }

        $users = \array_map(
            static fn(User $user): AnonymizedUser => $user->toAnonymizedUser(),
            $this->userRepository->findByIds(\array_values($data->users)),
        );

        return new SeasonResultWithUsersDto($data, $users);
    }

    /**
     * @param int|null $currentWeek null => cela sezona
     * @param int|null $activity null => vsechny aktivity dohromady
     */
    public function calculateSeasonResultRanking(
        Season $season,
        SeasonResultWithUsersDto $seasonResult,
        ?int $activity = null,
        ?int $currentWeek = null,
    ): SeasonResultRankDto {
        // za kazdy tyden se udeluje stejny pocet bodu(N)
        // tzn pokud se v prvnim tydnu zucastni 7 fakult a ve druhem tydnu 12 fakult, rozdeluje se i za prvni tyden 12 bodu
        // QUESTION(@bleksak): Je tohle opravdu co oni chteji? Kdyz to delali rucne, tak to spocitali za 1. tyden 7 fakult => 7 bodu, 2 tyden 12 bodu, ale nepamatuju si to uz

        /**
         * @technical:
         * 1.1. Vytvorit set unikatnich fakult skrz celou sezonu => pocet fakult v setu === pocet bodu za 1 tyden
         * 1.2. v kazdem tydnu -> seradit od nejvetsiho po nejmensi a rozdelovat N-i bodu => i je pozice fakulty
         * 1.3. pridat extra body
         * 1.4. pokud chceme vysledky za 1 tyden -> 1.x hotovo, jinak:
         * 1.5. clash tydnu
         * 2.1. pokud chceme vysledky za vsechny aktivity dohromady => 2.x hotovo, jinak:
         * 2.2. clash aktivit
         * 3. jeste jednou seradit kvuli tomu ze 2 fakutly muzou mit stejny pocet bodu => radime je potom podle (body, distance)
         */

        $facultySet = $this->createFacultySet($seasonResult);

        $ranking = [];
        $extras = [];

        if ($currentWeek === null) {
            foreach ($seasonResult->results as $week => $weeklyResult) {
                $this->populateRankingArray(
                    $facultySet,
                    $weeklyResult,
                    $ranking,
                    $extras,
                    $activity,
                );
            }
        } else {
            if ($currentWeek < 0 || $currentWeek >= $season->getWeekCount()) {
                return new SeasonResultRankDto(0, 0, [], []);
            }

            $weeklyResult = $seasonResult->results[$currentWeek];
            $this->populateRankingArray(
                $facultySet,
                $weeklyResult,
                $ranking,
                $extras,
                $activity,
            );
        }

        $result = [];

        $totalDistance = 0;
        $totalPoints = 0;

        foreach ($ranking as $row) {
            $result[] = new SeasonResultRankRowDto(
                $row['faculty'],
                $row['distance'],
                $row['points'],
            );

            $totalDistance += $row['distance'];
            $totalPoints += $row['points'];
        }

        \usort(
            $result,
            static fn(
                SeasonResultRankRowDto $a,
                SeasonResultRankRowDto $b,
            ): int => (
                $b->points <=> $a->points ?: $b->distance <=> $a->distance
            ),
        );

        return new SeasonResultRankDto(
            $totalDistance,
            $totalPoints,
            $result,
            $extras,
        );
    }

    /**
     * @return array<int, int>
     */
    private function createFacultySet(SeasonResultWithUsersDto $seasonResult): array
    {
        $facultySet = [];

        foreach ($seasonResult->results as $weeklyResult) {
            foreach ($weeklyResult->activities as $activityResult) {
                foreach ($activityResult->results as $facultyId => $_) {
                    $facultySet[$facultyId] = $facultyId;
                }
            }
        }

        return $facultySet;
    }

    /**
     * Step 1.x
     * @param array<int, int> $facultySet
     * @param array<int, array{points: int, distance: int, faculty: int}> $ranking
     * @param list<ExtraPointsDto> $extras
     */
    private function populateRankingArray(
        array $facultySet,
        WeeklyResultDto $weeklyResult,
        array &$ranking,
        array &$extras,
        ?int $allowedActivity = null,
    ): void {
        foreach ($weeklyResult->activities as $activityId => $activityResult) {
            if ($allowedActivity !== $activityId && $allowedActivity !== null) {
                continue;
            }

            // NOTE: uses copy on write
            $facultyResults = $activityResult->results;

            // TODO(@bleksak): tenhle sort mozna movnout do SeasonResultCalculatoru
            \usort(
                $facultyResults,
                static fn(FacultyResultDto $a, FacultyResultDto $b): int => (
                    $b->distance <=> $a->distance
                ),
            );

            for ($i = 0; $i < \count($facultyResults); ++$i) {
                $facultyResult = $facultyResults[$i];
                $facultyId = $facultyResult->faculty;

                $points = \count($facultySet) - $i;

                $ranking[$facultyId] ??= [
                    'distance' => 0,
                    'points' => 0,
                    'faculty' => $facultyId,
                ];

                $ranking[$facultyId]['distance'] += $facultyResult->distance;
                $ranking[$facultyId]['points'] += $points;
            }

            foreach ($activityResult->extras as $extra) {
                $ranking[$extra->faculty]['points'] += $extra->points;
                $extras[] = $extra;
            }
        }
    }
}
