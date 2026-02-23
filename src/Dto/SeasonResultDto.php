<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @import-type FacultyResultDtoType from FacultyResultDto
 * @import-type ActivityResultDtoType from ActivityResultDto
 * @import-type WeeklyResultDtoType from WeeklyResultDto
 * @import-type OutlierActivityDtoType from OutlierActivity
 *
 * @type SeasonResultDtoType = array{results: array<int, WeeklyResultDtoType>, outliers: array<int, OutlierActivityDtoType>, users: list<int>}
 */
final class SeasonResultDto
{
    /**
     * @param array<int, WeeklyResultDto> $results
     * @param array<int, OutlierActivity> $outliers
     * @param list<int> $users
     */
    public function __construct(
        public array $results,
        public array $outliers,
        public array $users = [],
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
            $data['users'],
        );
    }

    /**
     * @return SeasonResultDtoType
     */
    public function toArray(): array
    {
        return [
            'results' => \array_map(
                /** @return WeeklyResultDtoType */
                static fn(WeeklyResultDto $weeklyResult): array => $weeklyResult->toArray(),
                $this->results,
            ),
            'outliers' => \array_map(
                /** @return OutlierActivityDtoType */
                static fn(OutlierActivity $outlier): array => $outlier->toArray(),
                $this->outliers,
            ),
            'users' => $this->users,
        ];
    }
}
