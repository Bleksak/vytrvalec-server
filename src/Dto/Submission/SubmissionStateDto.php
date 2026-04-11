<?php

declare(strict_types=1);

namespace App\Dto\Submission;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class SubmissionStateDto
{
    #[OA\Property]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    public ?\DateTime $updatedAt = null;

    #[OA\Property]
    #[Assert\NotNull(message: 'blank')]
    #[Assert\Type(type: 'bool', message: 'invalid')]
    public bool $state = false;

    #[OA\Property]
    public string $message = '';
}
