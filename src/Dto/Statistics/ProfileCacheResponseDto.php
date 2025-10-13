<?php

declare(strict_types=1);

namespace App\Dto\Statistics;

final readonly class ProfileCacheResponseDto
{
    public function __construct(
        public int $activity,
        public int $distance,
        public int $elevation,
    ) {}
}
