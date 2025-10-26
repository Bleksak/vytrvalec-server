<?php

declare(strict_types=1);

namespace App\Dto;

final class OutlierResult
{
    public function __construct(
        public AnonymizedUser $user,
        public int $facultyId,
        public int $value,
    ) {}
}
