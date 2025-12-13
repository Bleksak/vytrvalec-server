<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @import-type FacultyResultDtoType from FacultyResultDto
 * @import-type ActivityResultDtoType from ActivityResultDto
 * @import-type WeeklyResultDtoType from WeeklyResultDto
 * @import-type OutlierActivityDtoType from OutlierActivity
 * @type SeasonResultDtoType = array{results: array<int, WeeklyResultDtoType>, outliers: list<OutlierActivityDtoType>}
 */
final class SeasonResultDto
{
    /**
     * @param array<int, WeeklyResultDto> $results
     * @param list<OutlierActivity> $outliers
     */
    public function __construct(
        public array $results,
        public array $outliers,
    ) {}

    /**
     * @param SeasonResultDtoType $data
     */
    public static function fromCache(array $data): self
    {
        return new self(
            \array_map(static function (mixed $weeklyResult): WeeklyResultDto {
                \assert(
                    isset($weeklyResult['week'], $weeklyResult['activities']),
                    'week and activities must be set in WeeklyResultDto',
                );

                return WeeklyResultDto::fromCache($weeklyResult);
            }, $data['results']),

            \array_map(OutlierActivity::fromCache(...), $data['outliers']),
        );
    }
}
