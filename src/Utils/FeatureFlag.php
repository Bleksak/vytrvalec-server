<?php

declare(strict_types=1);

namespace App\Utils;

enum FeatureFlag: string
{
    case ROLE_STAFF = 'ROLE_STAFF';
    case ROLE_USER = 'ROLE_USER';
    case FEATURE_EXPORT_SUBMISSIONS = 'EXPORT_SUBMISSIONS';
}
