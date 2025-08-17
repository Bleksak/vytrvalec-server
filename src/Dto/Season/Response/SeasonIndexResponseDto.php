<?php

declare(strict_types=1);

namespace App\Dto\Season\Response;

use App\Dto\Charity\Response\CharityGetResponseDto;

final readonly class SeasonIndexResponseDto
{
    public function __construct(
        public int $id,
        public CharityGetResponseDto $charity,
        public \DateTime $start,
        public \DateTime $end,
        public bool $canDelete,
        public bool $isRunning,
    ) {}
}
