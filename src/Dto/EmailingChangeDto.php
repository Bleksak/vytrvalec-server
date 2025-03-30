<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class EmailingChangeDto
{
    #[OA\Property]
    #[Assert\NotNull(message: 'blank')]
    #[Assert\Type(type: 'bool')]
    public bool $mailing;
}
