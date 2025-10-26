<?php

declare(strict_types=1);

namespace App\Dto\Submission\Response;

use App\Dto\User\Response\UserResponseDto;

final readonly class UnreviewedSubmissionResponseDto
{
    /**
     * @param array<int, SubmissionResponseDto> $submissions
     * @param array<int, UserResponseDto>       $users
     */
    public function __construct(
        public array $submissions,
        public array $users,
    ) {}
}
