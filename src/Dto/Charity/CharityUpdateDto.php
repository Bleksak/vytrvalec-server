<?php

declare(strict_types=1);

namespace App\Dto\Charity;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final class CharityUpdateDto
{
    #[OA\Property(
        type: 'string',
        example: 'David a Goliáš',
    )]
    public ?CharityUpdateTranslationDto $translations;

    #[OA\Property(
        type: 'string',
        example: 'https://davidagolias.cz/',
    )]
    #[Assert\Url(
        requireTld: false,
        message: 'invalid',
    )]
    public ?string $website = null;

    #[OA\Property(
        type: 'string',
        example: '019629dc-7636-7593-b215-50cec5259e2f',
    )]
    #[Assert\Type(
        type: 'string',
        message: 'invalid',
    )]
    public ?string $image = null;
}
