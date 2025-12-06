<?php

declare(strict_types=1);

namespace App\Dto;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

/**
 * @import-type FacultyResultDtoType from FacultyResultDto
 * @type ActivityResultDtoType = array{activity: int, results: array<int, FacultyResultDtoType>}
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
        public readonly array $results,
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

        return new self($data['activity'], $facultyResults);
    }
}
