<?php

declare(strict_types=1);

namespace App\Dto\Charity;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class CharityCreateDto
{
    #[OA\Property(type: 'string', example: 'David a Goliáš')]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    #[Assert\Type(type: 'string')]
    public string $name;

    #[OA\Property(type: 'string', example: 'Krátký text o charitě')]
    #[Assert\NotBlank(message: 'blank', allowNull: false)]
    #[Assert\Type(type: 'string')]
    public string $description;

    #[OA\Property(type: 'string', example: 'https://davidagolias.cz/')]
    #[Assert\Url(requireTld: false, message: 'invalid')]
    public ?string $website = null;

    #[OA\Property(type: 'string', example: '019629dc-7636-7593-b215-50cec5259e2f')]
    #[Assert\Uuid(message: 'invalid')]
    public ?Uuid $imageUuid = null;
}
