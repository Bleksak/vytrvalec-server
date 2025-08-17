<?php

declare(strict_types=1);

namespace App\Dto\Activity;

use App\Dto\TranslationObjectDto;
use OpenApi\Attributes as OA;

final class ActivityUpdateTranslationDto
{
    #[OA\Property]
    public ?TranslationObjectDto $name;
}
