<?php

declare(strict_types=1);

namespace App\Dto\Submission;

use App\Entity\Activity;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class SubmissionServerCreateDto
{
    public function __construct(
        #[OA\Property]
        #[Assert\GreaterThanOrEqual(0, message: 'negative')]
        #[Assert\Type(type: 'integer', message: 'invalid')]
        public ?int $elevation = null,

        #[OA\Property]
        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        #[Assert\GreaterThanOrEqual(1, message: 'negative')]
        #[Assert\Type(type: 'integer', message: 'invalid')]
        public ?int $distance = null,

        #[OA\Property]
        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        #[Assert\Uuid(message: 'invalid')]
        public ?Uuid $imageUuid = null,

        #[OA\Property]
        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        public ?Activity $activity = null,
        // #[OA\Property]
        // #[Assert\Type('datetime')]
        // #[Assert\NotBlank(allowNull: false)]
        // public \DateTime $date,
    ) {}
}
