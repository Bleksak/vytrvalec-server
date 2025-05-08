<?php

declare(strict_types=1);

namespace App\Dto;

final class WeeklySubmissionSum
{
    public function __construct(
        public int $distance,
        public int $faculty,
        public int $activity,
    ) {
    }
}
