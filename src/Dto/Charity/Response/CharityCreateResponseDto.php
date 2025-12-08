<?php

declare(strict_types=1);

namespace App\Dto\Charity\Response;

use App\Dto\TranslationObjectDto;
use OpenApi\Attributes as OA;

final class CharityCreateResponseDto
{
    public function __construct(
        #[OA\Property]
        public int $id,
        #[OA\Property]
        public TranslationObjectDto $name,
        #[OA\Property]
        public TranslationObjectDto $description,
        #[OA\Property]
        public ?string $image = null,
        #[OA\Property]
        public ?string $website = null,
    ) {}
}
