<?php

declare(strict_types=1);

namespace App\Schema;

use App\Entity\Activity;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(properties: [
    new OA\Property(
        property: 'activity',
        ref: new Model(type: Activity::class),
    ),
    new OA\Property(property: 'distance', type: 'integer'),
    new OA\Property(property: 'elevation', type: 'integer'),
])]
final class ProfileCacheSchema
{
}
