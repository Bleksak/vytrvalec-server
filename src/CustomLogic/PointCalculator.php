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
        $submissions = $this->submissionRepository->findAcceptedInSeason($season);
        return [];
    }

    public function processWeek($activities): array
    {
        foreach($activities as &$faculties) {
            usort($faculties, function($a, $b) {
                $cmp = $a['distance'] - $b['distance'];
                if($cmp !== 0) {
                    return $cmp;
                }

                return $a['elevation'] - $b['elevation'];
            });

            foreach($faculties as $key => &$faculty) {
                $faculty['points'] = $key + 1;
            }
        }

        return $activities;
    }
}