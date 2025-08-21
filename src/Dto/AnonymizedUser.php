<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class AnonymizedUser
{
    public ?string $lastName;

    public function __construct(
        public string $firstName,
        ?string $lastName,
        int|bool|null $anonymize,
    ) {
        $lastNameAnonymized = null;

        if ($anonymize !== null) {
            $lastNameAnonymized = $lastName;
        }

        $this->lastName = $lastNameAnonymized;
    }
}
