<?php

namespace App\Dto;

final class AnonymizedUserCandidate
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?bool $gdpr,
    ) {
    }

    public function anonymize(): AnonymizedUser
    {
        if ($this->gdpr) {
            return new AnonymizedUser(
                $this->firstName,
                $this->lastName
            );
        }

        return new AnonymizedUser(
            $this->firstName,
            null
        );
    }
}
