<?php

declare(strict_types=1);

namespace App\Utils;

enum SubmissionState: string
{
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Pending = 'pending';

    /**
     * @return list<self>
     */
    public static function reviewedStates(): array
    {
        return [self::Accepted, self::Rejected];
    }
}
