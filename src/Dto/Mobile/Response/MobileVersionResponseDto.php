<?php

declare(strict_types=1);

namespace App\Dto\Mobile\Response;

use OpenApi\Attributes as OA;

final class MobileVersionResponseDto
{
    public function __construct(
        #[OA\Property]
        public string $android,
        #[OA\Property]
        public string $ios,
    ) {
    }
}
