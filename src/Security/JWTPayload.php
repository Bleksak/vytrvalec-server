<?php

declare(strict_types=1);

namespace App\Security;

final readonly class JWTPayload
{
    public function __construct(
        public int $kid,
        public string $user,
        public int $exp,
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'kid' => $this->kid,
            'user' => $this->user,
            'exp' => $this->exp,
        ];
    }
}
