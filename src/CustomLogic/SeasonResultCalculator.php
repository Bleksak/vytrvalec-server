<?php

declare(strict_types=1);

namespace App\CustomLogic;

use App\Dto\ActivityResultDto;
use App\Dto\ExtraPointsDto;
use App\Dto\FacultyResultDto;
use App\Dto\SeasonResultDto;
use App\Dto\WeeklyResultDto;
use App\Entity\Season;
use App\Repository\FacultyMappingRepository;
use App\Repository\SubmissionRepository;
use App\Utils\WeekCalculator;
use DateTime;

final readonly class SeasonResultCalculator
{
    public function __construct(
        private SubmissionRepository $submissionRepository,
        private FacultyMappingRepository $facultyMappingRepository,
        private DailyDistanceExtraPoints $dailyDistanceExtraPoints,
        private WeeklyDistanceExtraPoints $weeklyDistanceExtraPoints,
        private WeeklyElevationExtraPoints $weeklyElevationExtraPoints,
    ) {}

    public function calculate(Season $season): SeasonResultDto
    {
        $facultyParentRoots =
            $this->facultyMappingRepository->findRootsBySeason($season);

        $weeks = WeekCalculator::calculateWeekCount(
            \DateTimeImmutable::createFromInterface($season->start),
            \DateTimeImmutable::createFromInterface($season->end),
        );

        $results = [];
        $users = [];

        /** @var list<ExtraPointsInterface> */
        $extraPointsClasses = [
            $this->dailyDistanceExtraPoints,
            $this->weeklyDistanceExtraPoints,
            $this->weeklyElevationExtraPoints,
        ];

        for ($i = 0; $i < $weeks; ++$i) {
            $weeklyResult = $this->submissionRepository->getResultsForWeek(
                $season,
                $i,
            );

            /** @var array<int, array<int, FacultyResultDto>> */
            $activities = [];

            foreach ($weeklyResult as $result) {
                $actualFaculty =
                    $facultyParentRoots[$result->faculty] ?? $result->faculty;

                $facultyResult =
                    $activities[$result->activity][$actualFaculty] ?? new FacultyResultDto(
                            $actualFaculty,
                            0,
                        );

                $facultyResult->distance += $result->distance;

                $activities[$result->activity][$actualFaculty] = $facultyResult;
            }

            $activityResult = [];

            foreach ($activities as $activityId => $activity) {
                $activityResult[$activityId] = new ActivityResultDto(
                    $activityId,
                    $activity,
                );
            }

            if (\count($activityResult) !== 0) {
                $results[$i] = new WeeklyResultDto($i, $activityResult);
            }
        }

        if ($season->end > new DateTime('2022-01-01')) {
            foreach ($extraPointsClasses as $cls) {
                $extras = $cls->calculate($season);
                foreach ($extras as $extra) {
                    $facultyId =
                        $facultyParentRoots[$extra->facultyId]
                            ?? $extra->facultyId;

                    $weeklyResult = $results[$cls->getWeek()] ?? null;
                    \assert($weeklyResult !== null);

                    $activityResult =
                        $weeklyResult->activities[$extra->activityId] ?? null;
                    \assert($activityResult !== null);

                    $activityResult->extras[] = new ExtraPointsDto(
                        $extra->user,
                        $facultyId,
                        $cls->getUniqueName(),
                        $extra->value,
                        $cls->reward(),
                        $extra->activityId,
                    );

                    $users[$extra->user] = $extra->user;
                }
            }
        }

        $topThree = $this->submissionRepository->findOutliers($season);

        foreach ($topThree as $outlier) {
            foreach ($outlier->results as $outlierResult) {
                \assert(isset($facultyParentRoots[$outlierResult->facultyId]));

                $outlierResult->facultyId =
                    $facultyParentRoots[$outlierResult->facultyId];

                $users[$outlierResult->user] = $outlierResult->user;
            }
        }

        return new SeasonResultDto($results, $topThree, \array_values($users));
    }
}
