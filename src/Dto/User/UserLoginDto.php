<?php

declare(strict_types=1);

namespace App\Dto\User;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class UserLoginDto
{
    public function __construct(
        #[OA\Property]
        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        #[Assert\Email(message: 'invalid')]
        public ?string $email = null,

        #[\SensitiveParameter]
        #[OA\Property]
        #[Assert\NotBlank(message: 'blank', allowNull: false)]
        #[Assert\PasswordStrength(
            message: 'invalid',
            minScore: Assert\PasswordStrength::STRENGTH_WEAK,
        )]
        public ?string $password = null,

        #[\SensitiveParameter]
        #[OA\Property]
        public ?string $firebaseToken = null,
    ) {}
}
