<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class AnonymizedUser
{
    public ?string $lastName = null;

    public function __construct(
        public string $firstName,
        ?string $lastName,
        int|bool|null $anonymize,
    ) {
        if ($anonymize !== null) {
            $this->lastName = $lastName;
        }
    }
}
