<?php

declare(strict_types=1);

namespace App\Dto\Submission;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class SubmissionEditDto
{
    public function __construct(
        #[Assert\GreaterThanOrEqual(1, message: 'negative')]
        #[Assert\Type(type: 'integer', message: 'invalid')]
        public ?int $distance = null,

        #[Assert\GreaterThanOrEqual(0, message: 'negative')]
        #[Assert\Type(type: 'integer', message: 'invalid')]
        public ?int $elevation = null,

        #[Assert\Uuid(message: 'invalid')]
        public ?Uuid $imageUuid = null,

        #[Assert\Type(type: 'integer')]
        #[Assert\GreaterThanOrEqual(1, message: 'negative')]
        public ?int $activityId = null,

        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        public ?\DateTime $updatedAt = null,
    ) {}
}
