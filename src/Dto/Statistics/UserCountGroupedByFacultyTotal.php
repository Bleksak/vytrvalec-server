<?php

declare(strict_types=1);

namespace App\Dto\Statistics;

use App\Dto\UserCountByFacultyStatistics;
use OpenApi\Attributes as OA;

final class UserCountGroupedByFacultyTotal
{
    /**
     * @param list<UserCountByFacultyStatistics> $users
     */
    public function __construct(
        #[OA\Property]
        public array $users,
        #[OA\Property(example: 70)]
        public int $total,
    ) {}
}
