<?php

declare(strict_types=1);

namespace App\Dto\Faculty;

use App\Dto\TranslationObjectDto;
use OpenApi\Attributes as OA;

final class FacultyCreateTranslationDto
{
    public function __construct(
        #[OA\Property]
        public TranslationObjectDto $name,
    ) {}
}
