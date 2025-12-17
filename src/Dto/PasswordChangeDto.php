<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PasswordChangeDto
{
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    public string $oldPassword;

    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    #[Assert\PasswordStrength(message: 'weak', minScore: 1)]
    public string $password;
}
