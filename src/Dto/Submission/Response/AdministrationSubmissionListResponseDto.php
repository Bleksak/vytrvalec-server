<?php

declare(strict_types=1);

namespace App\Dto\Submission\Response;

use App\Dto\User\Response\UserResponseDto;
use App\Entity\Submission;
use App\Services\ImagePath;
use OpenApi\Attributes as OA;

final class AdministrationSubmissionListResponseDto
{
    public function __construct(
        #[OA\Property(example: 1)]
        public int $id,
        #[OA\Property(example: true)]
        public bool $accepted,
        #[OA\Property(type: 'integer', example: 1)]
        public int $seasonId,
        #[OA\Property(type: 'integer', example: 1)]
        public UserResponseDto $user,
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
        #[OA\Property]
        public ?string $imageUuid,
        #[OA\Property(example: 'Dobrej vykon lil bro')]
        public string $message = '',
    ) {}

    public static function fromSubmission(
        Submission $submission,
        ?ImagePath $imagePath = null,
    ): self {
        return new self(
            $submission->id,
            $submission->accepted,
            $submission->season->id,
            $submission->user->toResponseObject(),
            $submission->elevation,
            $submission->distance,
            $submission->reviewed,
            $submission->image?->getPath($imagePath) ?? '',
            $submission->week,
            $submission->activity->id,
            $submission->date,
            $submission->updatedAt,
            $submission->image?->uuid->toString(),
            $submission->message,
        );
    }
}
