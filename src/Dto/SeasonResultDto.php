<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @import-type FacultyResultDtoType from FacultyResultDto
 * @import-type ActivityResultDtoType from ActivityResultDto
 * @import-type WeeklyResultDtoType from WeeklyResultDto
 * @import-type OutlierActivityDtoType from OutlierActivity
 *
 * TODO(@bleksak): tady odstranit null na users
 * @type SeasonResultDtoType = array{results: array<int, WeeklyResultDtoType>, outliers: list<OutlierActivityDtoType>, users: null|list<int>}
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
            $data['users'] ?? [],
        );
    }

    public function toArray(): array
    {
        $results = [];

        foreach ($this->results as $idx => $result) {
            $results[$idx] = $result->toArray();
        }

        return [
            'results' => $results,
            'outliers' => \array_map(
                static fn(OutlierActivity $outlier): array => $outlier->toArray(),
                $this->outliers,
            ),
        ];
    }
}
