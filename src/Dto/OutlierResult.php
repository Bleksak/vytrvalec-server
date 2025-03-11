<?php

namespace App\Dto;

final class OutlierResult
{
    public function __construct(
        public AnonymizedUser $user,
        public int $facultyId,
        public int $value,
    ) {
    }
}
