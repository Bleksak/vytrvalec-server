<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

/**
 * @import-type AnonymizedUserDtoType from AnonymizedUser
 * @type ExtraPointsDtoType = array{user: int|AnonymizedUserDtoType, name: string, faculty: int, value: int, points: int, activity: null|int}
 */
final class ExtraPointsDto
{
    public function __construct(
        #[OA\Property]
        public int|AnonymizedUser $user, // TODO(@bleksak): tohle nejakym zpusobem opravit, zmenit to pouze na int
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
        $user = \is_int($data['user'])
            ? $data['user']
            : AnonymizedUser::fromArray($data['user']);

        return new self(
            $user,
            $data['faculty'],
            $data['name'],
            $data['value'],
            $data['points'],
            $data['activity'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'user' => \is_int($this->user)
                ? $this->user
                : $this->user->toArray(),
            'faculty' => $this->faculty,
            'name' => $this->name,
            'value' => $this->value,
            'points' => $this->points,
            'activity' => $this->activity,
        ];
    }
}
