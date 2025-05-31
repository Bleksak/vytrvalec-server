<?php

declare(strict_types=1);

namespace App\Websocket\SubmissionProducer;

use App\Entity\User;

final class SubmissionProducerClient
{
    public ?User $user = null;
    public ?int $submissionId = null;

    public function __construct(
        public int $clientId,
    ) {
    }
}
