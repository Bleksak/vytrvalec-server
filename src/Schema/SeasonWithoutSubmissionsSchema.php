<?php

namespace App\Schema;

use App\Entity\Charity;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            example: 1
        ),
        new OA\Property(
            property: 'start',
            type: 'date',
            example: '2025-04-01',
        ),
        new OA\Property(
            property: 'end',
            type: 'date',
            example: '2025-05-01',
        ),
        new OA\Property(
            property: 'charity',
            ref: new Model(type: Charity::class),
        ),
    ]
)]
final class SeasonWithoutSubmissionsSchema
{
}
