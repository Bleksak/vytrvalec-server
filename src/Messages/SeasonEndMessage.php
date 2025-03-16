<?php

namespace App\Messages;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('delayed_rabbitmq')]
final class SeasonEndMessage
{
    public function __construct(
        public int $seasonId,
    ) {
    }
}
