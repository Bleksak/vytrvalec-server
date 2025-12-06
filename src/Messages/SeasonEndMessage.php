<?php

declare(strict_types=1);

namespace App\Messages;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
final class SeasonEndMessage
{
    public function __construct(
        public int $seasonId,
    ) {}
}
