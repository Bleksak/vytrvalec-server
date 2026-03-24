<?php

declare(strict_types=1);

namespace App\Dto\User\Response;

use OpenApi\Attributes as OA;

final readonly class UserListResponseDto
{
    /**
     * @param list<UserResponseDto> $data
     */
    public function __construct(
        #[OA\Property(
            type: 'array',
            items: new OA\Items(ref: UserResponseDto::class),
        )]
        public array $data,
        #[OA\Property]
        public int $total,
        #[OA\Property]
        public int $page,
        #[OA\Property]
        public int $limit,
    ) {}
}
