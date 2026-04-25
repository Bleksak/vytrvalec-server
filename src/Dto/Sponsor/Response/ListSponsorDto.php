<?php

declare(strict_types=1);

namespace App\Dto\Sponsor\Response;

use OpenApi\Attributes as OA;

final readonly class ListSponsorDto
{
    public function __construct(
        #[OA\Property]
        public string $name,
        #[OA\Property]
        public string $url,
        #[OA\Property]
        public string $image,
    ) {}
}
