<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

/**
 * @type FacultyResultDtoType = array{faculty: int, distance: int}
 */
final readonly class FacultyResultDto
{
    public function __construct(
        #[OA\Property(example: 1)]
        public int $faculty,
        #[OA\Property(example: 2250)]
        public int $distance,
    ) {}

    /**
     * @param FacultyResultDtoType $data
     */
    public static function fromCache(array $data): self
    {
        \assert(
            \is_int($data['faculty']),
            'faculty must be an integer in FacultyResultDto',
        );

        \assert(
            \is_int($data['distance']),
            'distance must be an integer in FacultyResultDto',
        );

        return new self($data['faculty'], $data['distance']);
    }

    public function toArray(): array
    {
        return [
            'faculty' => $this->faculty,
            'distance' => $this->distance,
        ];
    }
}
