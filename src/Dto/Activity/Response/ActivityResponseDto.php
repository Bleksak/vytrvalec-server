<?php

declare(strict_types=1);

namespace App\Dto\Activity\Response;

use App\Dto\TranslationObjectDto;

final readonly class ActivityResponseDto
{
    public function __construct(
        public int $id,
        public TranslationObjectDto $name,
        public ?string $icon,
        public bool $active,
        public int $minElevation,
    ) {
    }
}
