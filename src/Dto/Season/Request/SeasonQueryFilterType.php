<?php

declare(strict_types=1);

namespace App\Dto\Season\Request;

enum SeasonQueryFilterType: string
{
    case Date = 'date';
    case Week = 'week';
    case State = 'state';
    case User = 'user';
    case Faculty = 'faculty';
    case Activity = 'activity';
}
