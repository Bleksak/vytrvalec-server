<?php

declare(strict_types=1);

namespace App\Dto\Activity\Response;

final readonly class ActivityResponseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $active,
        public int $minElevation,
    ) {
    }
}
