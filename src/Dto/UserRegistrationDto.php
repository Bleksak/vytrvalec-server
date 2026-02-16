<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class UserRegistrationDto
{
    #[OA\Property]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    #[Assert\Email(message: 'invalid')]
    public string $email = '';

    #[OA\Property]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    #[Assert\PasswordStrength(
        message: 'weak',
        minScore: Assert\PasswordStrength::STRENGTH_WEAK,
    )]
    public string $password = '';

    #[OA\Property]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    public string $firstName = '';

    #[OA\Property]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    public string $lastName = '';

    #[OA\Property]
    #[Assert\NotBlank(message: 'invalid', allowNull: false)]
    #[Assert\Type(type: 'integer')]
    #[Assert\GreaterThan(value: 0, message: 'invalid')]
    public ?int $faculty = null;

    #[OA\Property]
    #[Assert\NotNull(message: 'blank')]
    #[Assert\Type(type: 'bool', message: 'invalid')]
    public bool $anonymize = false;

    #[OA\Property]
    #[Assert\NotNull(message: 'blank')]
    #[Assert\Type(type: 'bool', message: 'invalid')]
    public ?bool $gdpr = false;
}
