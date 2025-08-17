<?php

declare(strict_types=1);

namespace App\Websocket\SubmissionProducer;

final readonly class SubmissionProducerInitializeRequestPayload
{
    public function __construct(
        public string $jwt,
    ) {}
}
