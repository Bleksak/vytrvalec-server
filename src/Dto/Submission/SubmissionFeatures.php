<?php

declare(strict_types=1);

namespace App\Dto\Submission;

final class SubmissionFeatures
{
    public function __construct(
        public ?float $distance,
        public ?int $elevation,
        public ?\DateTime $date,
    ) {}
}
