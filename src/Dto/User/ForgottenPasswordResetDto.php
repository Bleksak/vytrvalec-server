<?php

declare(strict_types=1);

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

final class ForgottenPasswordResetDto
{
    #[Assert\NotBlank(allowNull: false)]
    #[Assert\PasswordStrength(
        message: 'weak',
        minScore: Assert\PasswordStrength::STRENGTH_WEAK,
    )]
    public string $password;

    public function __construct(
        #[Assert\NotBlank(allowNull: false)]
        #[\SensitiveParameter]
        public string $passwordResetToken,
    ) {}
}
