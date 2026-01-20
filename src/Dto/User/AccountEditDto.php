<?php

declare(strict_types=1);

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

final class AccountEditDto
{
    public function __construct(
        #[Assert\NotBlank]
        public bool $mailing,
        #[Assert\NotBlank]
        public bool $anonymize,

        public string $currentPassword = '',
        public string $newPassword = '',
    ) {}
}
