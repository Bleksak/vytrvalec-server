<?php

declare(strict_types=1);

namespace App\Dto\Charity;

use App\Dto\TranslationObjectDto;
use OpenApi\Attributes as OA;

final class CharityCreateTranslationDto
{
    public function __construct(
        #[OA\Property]
        public TranslationObjectDto $name,
        #[OA\Property]
        public TranslationObjectDto $description,
    ) {}
}
