<?php

namespace App\CustomLogic;

use App\Entity\Season;
use App\Repository\ActivityRepository;
use App\Repository\FacultyRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;

class PointCalculator
{
    public function __construct(private readonly UserRepository $userRepository, private ActivityRepository $activityRepository, private FacultyRepository $facultyRepository, private SubmissionRepository $submissionRepository)
    {
    }
    public function processSeason(Season $season): array
    {
        $weeks = [];

        for($week = 0; $week < 4; ++$week) {
            $weeks[] = $this->processWeek($season, $week);
        }

        return $weeks;
    }

    public function processWeek(Season $season, int $week): array
    {
        $submissions = $this->submissionRepository->findAcceptedInSeasonAndWeek($season, $week);

        $extraPointClasses = [WeeklyDistanceExtraPoints::class, DailyDistanceExtraPoints::class, WeeklyElevationExtraPoints::class];
        $extraPoints = [];
        $activities = [];

        foreach($submissions as $submission) {
            $activity = $submission->getActivity()->getId();
            $faculty = $submission->getFaculty()->getId();

            if(!array_key_exists($activity, $activities)) {
                $activities[$activity] = ['faculties' => []];
            }

            if(!array_key_exists($activity, $extraPoints)) {
                $extraPoints[$activity] = [];
                $activities[$activity]['extras'] = [];

                foreach($extraPointClasses as $extra) {
                    if($extra::acceptsWeek($week)) {
                        $cls = new $extra();
                        $extraPoints[$activity][] = $cls;
                        if($cls->requiresActivity()) {
                            $cls->setActivity($submission->getActivity());
                        }
                    }
                }
            }

            if(!array_key_exists($faculty, $activities[$activity])) {
                $activities[$activity]['faculties'][$faculty] = 0;
            }

            $activities[$activity]['faculties'][$faculty] += $submission->getDistance();

            foreach($extraPoints[$activity] as $extra) {
                $extra->accumulate($submission);
            }
        }

        foreach($extraPoints as $activityId => $activity) {
            foreach($activity as $extraPointHandler) {
                $extraPointHandler->finalize();
                $result = $extraPointHandler->getWinners();

                if(!empty($result)) {
                    $activities[$activityId]['extras'][] = $result;
                }
            }
        }

        return $activities;
    }
}
