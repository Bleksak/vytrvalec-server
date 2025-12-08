<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

final class TranslationObjectDto
{
    public function __construct(
        #[OA\Property]
        public ?string $cs,
        #[OA\Property]
        public ?string $en,
    ) {}

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'cs' => $this->cs,
            'en' => $this->en,
        ];
    }

    /**
     * @param array<string, string> $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['cs'] ?? null, $data['en'] ?? null);
    }
}
