<?php

declare(strict_types=1);

namespace App\Dto\Submission;

use App\Entity\Activity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class SubmissionServerEditDto
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

        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        public ?Activity $activity = null,

        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        public ?\DateTime $updatedAt = null,
    ) {}
}
