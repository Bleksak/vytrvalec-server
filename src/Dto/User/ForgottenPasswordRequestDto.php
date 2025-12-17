<?php

declare(strict_types=1);

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

final class ForgottenPasswordRequestDto
{
    #[Assert\Email(message: 'validation.email.invalid')]
    #[Assert\NotBlank(message: 'validation.email.not_blank', allowNull: false)]
    public ?string $email = null;
}
