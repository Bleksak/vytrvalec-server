<?php

declare(strict_types=1);

namespace App\Websocket\SubmissionProducer;

enum SubmissionProducerMessageType: string
{
    case InitializeRequest = 'initialize';
    case SubmissionRequest = 'request_submission';
    case SubmissionReviewRequest = 'review_submission';
    case Success = 'ok';
    case Fail = 'nok';
}
