<?php

declare(strict_types=1);

namespace App\Dto\Season;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class SeasonUpdateDto
{
    #[OA\Property(example: '2025-04-01')]
    #[Assert\Type('datetime')]
    #[Assert\NotBlank(allowNull: false)]
    public \DateTime $start;

    #[OA\Property(example: '2025-05-01')]
    #[Assert\Type('datetime')]
    #[Assert\NotBlank(allowNull: false)]
    public \DateTime $end;

    #[OA\Property]
    #[Assert\NotNull(message: 'blank')]
    #[Assert\Type(type: 'bool', message: 'invalid')]
    public bool $isTest = false;
}
