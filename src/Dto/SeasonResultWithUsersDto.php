<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class SeasonResultWithUsersDto
{
    /** @var array<int, WeeklyResultDto> */
    public array $results;

    /** @var array<int, OutlierActivity> */
    public array $outliers;

    /**
     * @param array<int, AnonymizedUser> $users
     */
    public function __construct(
        SeasonResultDto $seasonResult,
        public array $users,
    ) {
        $this->results = $seasonResult->results;
        $this->outliers = $seasonResult->outliers;
    }
}
