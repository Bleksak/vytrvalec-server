<?php

declare(strict_types=1);

namespace App\Dto\Extract;

use App\Utils\SubmissionState;

final class ExtractSubmissionDto
{
    public function __construct(
        public int $activityId,
        public int $seasonId,
        public SubmissionState $state,
        public int $distance,
        public int $elevation,
        public string $image,
    ) {}
}
