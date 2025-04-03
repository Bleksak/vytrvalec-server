<?php

namespace App\Dto;

final class WeeklySubmissionSum
{
    public function __construct(
        public string $distance,
        public int $faculty,
        public int $activity,
    ) {
    }
}
