<?php

declare(strict_types=1);

namespace App\Websocket\SubmissionProducer;

final class SubmissionProducerReviewPayload
{
    public function __construct(
        public bool $accepted,
        public string $message = '',
    ) {}
}
