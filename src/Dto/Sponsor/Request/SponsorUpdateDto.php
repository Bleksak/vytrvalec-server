<?php

declare(strict_types=1);

namespace App\Dto\Sponsor\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SponsorUpdateDto
{
    public function __construct(
        #[OA\Property]
        #[Assert\NotBlank(allowNull: false, message: 'blank')]
        public ?string $name = null,

        #[OA\Property]
        #[Assert\NotBlank(allowNull: false, message: 'blank')]
        #[Assert\Url(message: 'invalid')]
        public ?string $url = null,

        #[OA\Property]
        #[Assert\Uuid(message: 'invalid')]
        public ?Uuid $image = null,
    ) {}
}
