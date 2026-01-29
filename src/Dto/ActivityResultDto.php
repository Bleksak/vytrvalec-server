<?php

declare(strict_types=1);

namespace App\Dto;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

/**
 * @import-type FacultyResultDtoType from FacultyResultDto
 * @import-type ExtraPointsDtoType from ExtraPointsDto
 * @type ActivityResultDtoType = array{activity: int, results: array<int, FacultyResultDtoType>, extras: list<ExtraPointsDtoType>}
 */
final class ActivityResultDto
{
    /**
     * @var list<ExtraPointsDto>
     **/
    #[OA\Property(
        type: 'array',
        items: new OA\Items(ref: new Model(type: ExtraPointsDto::class)),
    )]
    public array $extras = [];

    /**
     * @param array<int, FacultyResultDto> $results
     **/
    public function __construct(
        #[OA\Property(example: 1)]
        public readonly int $activity,
        #[OA\Property(
            type: 'array',
            items: new OA\Items(ref: new Model(type: FacultyResultDto::class)),
        )]
        public array $results,
    ) {}

    /**
     * @param ActivityResultDtoType $data
     */
    public static function fromCache(array $data): self
    {
        $results = $data['results'];

        $facultyResults = [];
        foreach ($results as $facultyId => $facultyResult) {
            $facultyResults[$facultyId] =
                FacultyResultDto::fromCache($facultyResult);
        }

        $result = new self($data['activity'], $facultyResults);
        $extras = [];

        foreach ($data['extras'] as $extra) {
            $extras[] = ExtraPointsDto::fromCache($extra);
        }

        $result->extras = $extras;

        return $result;
    }

    public function toArray(): array
    {
        $results = [];
        foreach ($this->results as $idx => $result) {
            $results[$idx] = $result->toArray();
        }

        return [
            'results' => $results,
            'activity' => $this->activity,
            'extras' => \array_map(
                static fn(ExtraPointsDto $extraPoints): array => $extraPoints->toArray(),
                $this->extras,
            ),
        ];
    }
}
