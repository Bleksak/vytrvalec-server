<?php

declare(strict_types=1);

namespace App\Websocket\SubmissionProducer;

use App\Utils\SubmissionState;

final class SubmissionProducerReviewPayload
{
    public function __construct(
        public SubmissionState $state,
        public string $message = '',
    ) {}
}
