<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UserAccountChangeDto
{
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    public string $oldPassword;

    #[Assert\PasswordStrength(message: 'weak', minScore: 1)]
    public ?string $password = null;

    #[Assert\Type(type: 'boolean')]
    public ?bool $mailing = null;
}
