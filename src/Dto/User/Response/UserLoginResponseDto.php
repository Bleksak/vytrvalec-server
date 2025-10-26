<?php

declare(strict_types=1);

namespace App\Dto\User\Response;

use OpenApi\Attributes as OA;
use SensitiveParameter;

final readonly class UserLoginResponseDto
{
    public function __construct(
        #[OA\Property]
        public UserResponseDto $user,
        #[OA\Property]
        #[SensitiveParameter]
        public string $token,
    ) {}
}
