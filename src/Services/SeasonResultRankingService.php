<?php

declare(strict_types=1);

namespace App\Services;

use App\CustomLogic\SeasonResultCalculator;
use App\Dto\FacultyResultDto;
use App\Dto\SeasonResult\SeasonResultRankDto;
use App\Dto\SeasonResultDto;
use App\Dto\WeeklyResultDto;
use App\Entity\Season;
use App\Repository\SeasonCacheRepository;

final readonly class SeasonResultRankingService
{
    public function __construct(
        private SeasonCacheRepository $seasonCacheRepository,
        private SeasonResultCalculator $seasonResultCalculator,
    ) {}

    public function getSeasonResult(Season $season): SeasonResultDto
    {
        $cache = $this->seasonCacheRepository->findOneBy(['season' =>
            $season->getId()]);

        if ($cache !== null) {
            return $cache->getData();
        }

        return $this->seasonResultCalculator->calculate($season);
    }

    /**
     * @param int|null $week null => cela sezona
     * @param int|null $activity null => vsechny aktivity dohromady
     *
     * @return list<SeasonResultRankDto>
     */
    public function calculateSeasonResultRanking(
        Season $season,
        SeasonResultDto $seasonResult,
        ?int $activity = null,
        ?int $week = null,
    ): array {
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

        if ($week === null) {
            foreach ($seasonResult->results as $week => $weeklyResult) {
                $this->populateRankingArray(
                    $facultySet,
                    $weeklyResult,
                    $ranking,
                    $activity,
                );
            }
        } else {
            if ($week < 0 || $week >= $season->getWeekCount()) {
                return [];
            }

            $weeklyResult = $seasonResult->results[$week];
            $this->populateRankingArray(
                $facultySet,
                $weeklyResult,
                $ranking,
                $activity,
            );
        }

        $result = [];

        foreach ($ranking as $row) {
            $result[] = new SeasonResultRankDto(
                $row['faculty'],
                $row['distance'],
                $row['points'],
            );
        }

        \usort(
            $result,
            static fn(SeasonResultRankDto $a, SeasonResultRankDto $b): int => (
                $b->points <=> $a->points
                ?: $b->distance <=> $a->distance
            ),
        );

        return $result;
    }

    /**
     * @return array<int, int>
     */
    private function createFacultySet(SeasonResultDto $seasonResult): array
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
     */
    private function populateRankingArray(
        array $facultySet,
        WeeklyResultDto $weeklyResult,
        array &$ranking,
        ?int $allowedActivity = null,
    ): void {
        foreach ($weeklyResult->activities as $activityId => $activityResult) {
            if ($allowedActivity !== $activityId && $allowedActivity !== null) {
                continue;
            }

            $facultyResults = [...$activityResult->results];

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
                // TODO(@bleksak): tady vyresit jeste collect toho uzivatele
                $ranking[$extra->faculty]['points'] += $extra->points;
            }
        }
    }
}
