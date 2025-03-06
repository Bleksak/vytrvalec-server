<?php

namespace App\Dto;

final class AnonymizedUser
{
    public function __construct(
        public readonly string $firstName,
        public readonly ?string $lastName,
    ) {
    }
}
