<?php

declare(strict_types=1);

namespace App\Dto\User\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UserSearchDto
{
    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public int $page = 1,
        #[Assert\Range(min: 1, max: 100)]
        public int $limit = 25,
        public string $search = '',
    ) {}
}
