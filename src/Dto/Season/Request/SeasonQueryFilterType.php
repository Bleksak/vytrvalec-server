<?php

declare(strict_types=1);

namespace App\Dto\Season\Request;

enum SeasonQueryFilterType: string
{
    case Date = 'date';
    case Week = 'week';
    case Accepted = 'accepted';
    case Reviewed = 'reviewed';
    case User = 'user';
    case Faculty = 'faculty';
    case Activity = 'activity';
    case Page = 'page';
}
