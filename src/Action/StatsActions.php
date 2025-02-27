<?php

namespace App\Action;

use App\Dto\TotalStatisticsDto;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;

final class StatsActions
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function getTotalStatistics(): TotalStatisticsDto
    {
        $users = $this->userRepository->getActiveUsersCount();
        $activities = $this->submissionRepository->getTotalStatistics();

        return new TotalStatisticsDto(
            $users,
            $activities
        );
    }
}
