<?php

namespace App\Action;

use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;

class StatsActions
{
    public function __construct(
        private readonly SubmissionRepository $submissionRepository,
        private readonly UserRepository $userRepository,
    )
    {
    }

    /**
     * @return array<string, array>
     */
    public function getTotalStatistics(): array
    {
        $users = $this->userRepository->getActiveUsersCount();
        $activities = $this->submissionRepository->getTotalStatistics();

        return [
            'users' => $users,
            'activities' => $activities
        ];
    }
}
