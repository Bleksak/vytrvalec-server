<?php

declare(strict_types=1);

namespace App\Dto\Charity\Response;

use OpenApi\Attributes as OA;

final class CharityGetResponseDto
{
    public function __construct(
        #[OA\Property]
        public int $id,
        #[OA\Property]
        public string $name,
        #[OA\Property]
        public string $description,
        #[OA\Property]
        public ?string $image = null,
        #[OA\Property]
        public ?string $website = null,
    ) {
    }
}
