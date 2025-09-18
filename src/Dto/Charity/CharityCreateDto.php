<?php

declare(strict_types=1);

namespace App\Dto\Charity;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class CharityCreateDto
{
    #[OA\Property]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    public CharityCreateTranslationDto $translations;

    #[OA\Property(type: 'string', example: 'https://davidagolias.cz/')]
    #[Assert\Url(requireTld: false, message: 'invalid')]
    public ?string $website = null;

    #[OA\Property(type: 'string', example: '019629dc-7636-7593-b215-50cec5259e2f')]
    #[Assert\Uuid(message: 'invalid')]
    public ?Uuid $imageUuid = null;
}
