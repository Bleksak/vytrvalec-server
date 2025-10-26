<?php

declare(strict_types=1);

namespace App\Messages;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('delayed_rabbitmq')]
final class SeasonStartMessage
{
    public function __construct(
        public int $seasonId,
    ) {}
}
