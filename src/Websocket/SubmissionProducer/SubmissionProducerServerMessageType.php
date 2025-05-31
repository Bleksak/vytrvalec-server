<?php

declare(strict_types=1);

namespace App\Websocket\SubmissionProducer;

enum SubmissionProducerServerMessageType: string
{
    case SubmissionResponse = 'submission_response';
    case SubmissionReviewRejected = 'submission_review_rejected';
    case SubmissionReviewAccepted = 'submission_review_accepted';
    case NoSubmissionsResponse = 'no_submissions';
}
