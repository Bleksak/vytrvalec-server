<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\Statistics\UserCountGroupedByFacultyTotal;
use App\Dto\TotalStatisticsDto;
use App\Entity\Season;
use App\Repository\ActivityRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use App\Services\ImagePath;

final readonly class StatisticsActions
{
    public function __construct(
        private SubmissionRepository $submissionRepository,
        private ActivityRepository $activityRepository,
        private UserRepository $userRepository,
        private ImagePath $imagePath,
    ) {}

    public function getTotalStatistics(?string $locale = null): TotalStatisticsDto
    {
        $usersFrom2020 = 1024;
        $usersFrom2021 = 357;

        $users =
            $usersFrom2020
            + $usersFrom2021
            + $this->submissionRepository->sumCountUserGroupedByFaculties();

        $activities = $this->activityRepository->getTotalStatistics(
            $this->imagePath,
            $locale,
        );

        return new TotalStatisticsDto($users, $activities);
    }

    public function getUserCountByFaculties(Season $season): UserCountGroupedByFacultyTotal
    {
        return $this->userRepository->countUserGroupedByFaculties($season);
    }
}
