<?php

declare(strict_types=1);

namespace App\Dto\User;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class PasswordResetRequestDto
{
    #[OA\Property]
    #[Assert\NotBlank(
        message: 'blank',
        allowNull: false,
    )]
    #[Assert\Email(message: 'invalid')]
    public string $email;
}
