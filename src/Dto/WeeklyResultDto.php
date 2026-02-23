<?php

declare(strict_types=1);

namespace App\Dto;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

/**
 * @import-type FacultyResultDtoType from FacultyResultDto
 * @import-type ActivityResultDtoType from ActivityResultDto
 * @type WeeklyResultDtoType = array{week: int, activities: array<int, ActivityResultDtoType>}
 */
final class WeeklyResultDto
{
    /**
     * @param array<int, ActivityResultDto> $activities
     */
    public function __construct(
        #[OA\Property(example: 2)]
        public readonly int $week,
        #[OA\Property(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ActivityResultDto::class)),
        )]
        public array $activities,
    ) {}

    /**
     * @param WeeklyResultDtoType $data
     */
    public static function fromCache(array $data): self
    {
        \assert(
            \is_int($data['week']),
            'week must be numeric in WeeklyResultDto',
        );

        \assert(
            \is_array($data['activities']),
            'activities must be an array in WeeklyResultDto',
        );

        $activities = [];

        foreach ($data['activities'] as $activityId => $activity) {
            $activities[$activityId] = ActivityResultDto::fromCache($activity);
        }

        return new self($data['week'], $activities);
    }

    /**
     * @return WeeklyResultDtoType
     */
    public function toArray(): array
    {
        return [
            'activities' => \array_map(
                /** @return ActivityResultDtoType */
                static fn(ActivityResultDto $activityResultDto): array => $activityResultDto->toArray(),
                $this->activities,
            ),
            'week' => $this->week,
        ];
    }
}
