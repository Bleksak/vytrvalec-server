<?php

declare(strict_types=1);

namespace App\Action;

use App\Dto\TotalStatisticsDto;
use App\Dto\UserCountByFacultyStatistics;
use App\Entity\Season;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;

final readonly class StatisticsActions
{
    public function __construct(
        private SubmissionRepository $submissionRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function getTotalStatistics(): TotalStatisticsDto
    {
        $usersFrom2020 = 1024;
        $usersFrom2021 = 357;

        $users = $usersFrom2020 + $usersFrom2021 + $this->submissionRepository->sumCountUserGroupedByFaculties();
        $activities = $this->submissionRepository->getTotalStatistics();

        return new TotalStatisticsDto(
            $users,
            $activities
        );
    }

    /**
     * @return array<UserCountByFacultyStatistics>
     */
    public function getUserCountByFaculties(Season $season): array
    {
        return $this->userRepository->countUserGroupedByFaculties($season);
    }
}
