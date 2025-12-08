<?php

declare(strict_types=1);

namespace App\Dto\Submission;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class SubmissionCreateDto
{
    #[OA\Property]
    #[Assert\GreaterThanOrEqual(0, message: 'negative')]
    #[Assert\Type(
        type: 'integer',
        message: 'invalid',
    )]
    public ?int $elevation = null;

    #[OA\Property]
    #[Assert\NotBlank(
        message: 'blank',
        allowNull: false,
    )]
    #[Assert\GreaterThanOrEqual(1, message: 'negative')]
    #[Assert\Type(
        type: 'integer',
        message: 'invalid',
    )]
    public int $distance;

    #[OA\Property]
    #[Assert\NotBlank(
        message: 'blank',
        allowNull: false,
    )]
    #[Assert\Uuid(message: 'invalid')]
    public Uuid $imageUuid;

    #[OA\Property]
    #[Assert\NotBlank(
        message: 'blank',
        allowNull: false,
    )]
    #[Assert\Type(type: 'integer')]
    #[Assert\GreaterThanOrEqual(1, message: 'negative')]
    public int $activityId;

    // #[OA\Property]
    // #[Assert\Type('datetime')]
    // #[Assert\NotBlank(allowNull: false)]
    // public \DateTime $date;
}
