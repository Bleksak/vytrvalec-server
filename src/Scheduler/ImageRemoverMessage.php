<?php

declare(strict_types=1);

namespace App\Scheduler;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
final class ImageRemoverMessage
{
}
