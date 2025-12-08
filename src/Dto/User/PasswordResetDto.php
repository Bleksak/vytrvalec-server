<?php

declare(strict_types=1);

namespace App\Dto\User;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class PasswordResetDto
{
    #[OA\Property]
    #[Assert\NotBlank(
        message: 'blank',
        allowNull: false,
    )]
    public string $password;

    #[OA\Property]
    #[Assert\NotBlank(
        message: 'blank',
        allowNull: false,
    )]
    public string $passwordResetToken;
}
