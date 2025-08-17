<?php

declare(strict_types=1);

namespace App\Dto;

final class OutlierActivity
{
    /**
     * @param array<int, OutlierResult> $results
     */
    public function __construct(
        public int $activityId,
        public array $results,
    ) {}
}
