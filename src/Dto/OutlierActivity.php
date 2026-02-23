<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * @import-type OutlierResultDtoType from OutlierResult
 * @type OutlierActivityDtoType = array{activityId: int, results: array<int, OutlierResultDtoType>}
 */
final class OutlierActivity
{
    /**
     * @param array<int, OutlierResult> $results
     */
    public function __construct(
        public int $activityId,
        public array $results,
    ) {}

    /**
     * @param OutlierActivityDtoType $data
     */
    public static function fromCache(array $data): self
    {
        \assert(\is_int($data['activityId']), 'activityId must be an int');
        \assert(\is_array($data['results']), 'results must be an array');

        return new self($data['activityId'], \array_map(
            static function (mixed $result): OutlierResult {
                \assert(
                    isset(
                        $result['user'],
                        $result['facultyId'],
                        $result['value'],
                    ),
                    'array must contain user, facultyId, value',
                );

                return OutlierResult::fromCache($result);
            },
            $data['results'],
        ));
    }

    /**
     * @return OutlierActivityDtoType
     */
    public function toArray(): array
    {
        return [
            'activityId' => $this->activityId,
            'results' => \array_map(
                /** @return OutlierResultDtoType */
                static fn(OutlierResult $result): array => $result->toArray(),
                $this->results,
            ),
        ];
    }
}
