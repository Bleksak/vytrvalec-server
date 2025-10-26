<?php

declare(strict_types=1);

namespace App\Dto\Faculty;

use App\Dto\TranslationObjectDto;
use OpenApi\Attributes as OA;

final class FacultyUpdateTranslationDto
{
    #[OA\Property]
    public ?TranslationObjectDto $name;
}
