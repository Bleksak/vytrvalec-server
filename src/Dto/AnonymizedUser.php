<?php

declare(strict_types=1);

namespace App\Dto;

final class AnonymizedUser
{
    public readonly ?string $lastName;

    public function __construct(
        public readonly string $firstName,
        ?string $lastName,
        int|bool|null $gdpr,
    ) {
        if ($gdpr == false) {
            $this->lastName = null;
        } else {
            $this->lastName = $lastName;
        }
    }
}
