<?php

declare(strict_types=1);

namespace App\Dto\Submission\Response;

use OpenApi\Attributes as OA;

final class SubmissionResponseDto
{
    public function __construct(
        #[OA\Property(example: 1)]
        public int $id,
        #[OA\Property(example: true)]
        public bool $accepted,
        #[OA\Property(type: 'integer', example: 1)]
        public int $seasonId,
        #[OA\Property(type: 'integer', example: 1)]
        public int $userId,
        #[OA\Property(type: 'integer', example: 1500)]
        public int $elevation,
        #[OA\Property(type: 'integer', example: 1500)]
        public int $distance,
        #[OA\Property(example: true)]
        public bool $reviewed,
        #[OA\Property]
        public ?string $image,
        #[OA\Property(example: 2)]
        public int $week,
        #[OA\Property(type: 'integer', example: 1)]
        public int $activityId,
        #[OA\Property(type: 'string', format: 'date', example: '2025-04-11')]
        public \DateTime $date,
        #[OA\Property(type: 'string', format: 'date-time', example: 1)]
        public \DateTime $updatedAt,
        #[OA\Property(example: 'Dobrej vykon lil bro')]
        public string $message = '',
        #[OA\Property]
        public ?string $imageUuid,
    ) {}
}
