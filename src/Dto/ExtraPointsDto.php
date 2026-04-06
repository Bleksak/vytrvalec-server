<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

/**
 * @type ExtraPointsDtoType = array{user: int, name: string, faculty: int, value: int, points: int, activity: int}
 */
final class ExtraPointsDto
{
    public function __construct(
        #[OA\Property]
        public int $user,
        #[OA\Property(example: 1)]
        public int $faculty,
        #[OA\Property(example: 'daily_distance')]
        public string $name,
        #[OA\Property(example: 2700)]
        public int $value,
        #[OA\Property(example: 1)]
        public int $points,
        #[OA\Property(example: 1)]
        public int $activity,
    ) {}

    /**
     * @param ExtraPointsDtoType $data
     */
    public static function fromCache(array $data): self
    {
        return new self(
            $data['user'],
            $data['faculty'],
            $data['name'],
            $data['value'],
            $data['points'],
            $data['activity'],
        );
    }

    /**
     * @return ExtraPointsDtoType
     */
    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'faculty' => $this->faculty,
            'name' => $this->name,
            'value' => $this->value,
            'points' => $this->points,
            'activity' => $this->activity,
        ];
    }
}
