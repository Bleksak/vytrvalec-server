<?php

namespace App\CustomLogic;

use App\Entity\Season;
use App\Repository\ActivityRepository;
use App\Repository\FacultyRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use App\Dto\ActivityResultDto;

class PointCalculator
{
    public function __construct(private readonly UserRepository $userRepository, private ActivityRepository $activityRepository, private FacultyRepository $facultyRepository, private SubmissionRepository $submissionRepository)
    {
    }

    /**
    * @return array<int, array<int, ActivityResultDto>>
    */
    public function processSeason(Season $season): array
    {
        $weeks = [];

        for($week = 0; $week < 4; ++$week) {
            $weekResults = $this->processWeek($season, $week);
            if(!empty($weekResults)) {
                $weeks[] = $weekResults;
            }
        }

        return $weeks;
    }

    /**
    * @return array<int, ActivityResultDto>
    */
    public function processWeek(Season $season, int $week): array
    {
        $submissions = $this->submissionRepository->findAcceptedInSeasonAndWeek($season, $week);

        $extraPointClasses = [WeeklyDistanceExtraPoints::class, DailyDistanceExtraPoints::class, WeeklyElevationExtraPoints::class];
        $extraPoints = [];
        $activities = [];

        $activitiesMapping = [];
        $facultiesMapping = [];

        foreach($submissions as $submission) {
            $activity = $submission->getActivity();
            $faculty = $submission->getUser()->getFaculty();

            if(!array_key_exists($activity, $activities)) {
                $activitiesMapping[$activity->getId()] = $activity;
                
                // $extraPoints[$activity->getId()] = [];

                // foreach($extraPointClasses as $extra) {
                //     if($extra::acceptsWeek($week)) {
                //         $cls = new $extra();
                //         $extraPoints[$activity->getId()][] = $cls;
                //         if($cls->requiresActivity()) {
                //             $cls->setActivity($activity);
                //         }
                //     }
                // }
            }

            if(!array_key_exists($faculty, $activities[$activity->getId()])) {
                $activities[$activity->getId()][$faculty->getId()] = 0;
                $facultiesMapping[$faculty->getId()] = $faculty;
            }

            $activities[$activity->getId()][$faculty->getId()] += $submission->getDistance();

            // foreach($extraPoints[$activity] as $extra) {
            //     $extra->accumulate($submission);
            // }
        }

        // foreach($extraPoints as $activityId => $activity) {
        //     foreach($activity as $extraPointHandler) {
        //         $extraPointHandler->finalize();
        //         $result = $extraPointHandler->getWinners();
                
        //         if(!empty($result)) {
        //             $activities[$activityId]['extras'][] = $result;
        //         }
        //     }
        // }

        foreach($activities as $activityId => $activityResult) {
            foreach($activityResult as $facultyId => $facultyResult) {
                
            }
        }

        return $activities;
    }
}
