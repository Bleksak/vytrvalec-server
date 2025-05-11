<?php

declare(strict_types=1);

namespace App\Dto\User;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class UserLoginDto
{
    #[OA\Property]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    #[Assert\Email(message: 'invalid')]
    public string $email;

    #[OA\Property]
    #[Assert\NotBlank(message: 'blank')]
    #[Assert\PasswordStrength(message: 'invalid', minScore: Assert\PasswordStrength::STRENGTH_WEAK)]
    public string $password;

    #[OA\Property]
    public ?string $firebaseToken = null;
}
