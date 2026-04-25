<?php

declare(strict_types=1);

namespace App\Services\Sponsor;

use App\Dto\Sponsor\Response\ListSponsorDto;
use App\Dto\Sponsor\Response\SponsorDetailDto;
use App\Entity\Season;
use App\Entity\Sponsor;
use App\Services\ImagePath;

final readonly class SponsorDtoMapper
{
    public function __construct(
        private ImagePath $imagePath,
    ) {}

    /**
     * @param list<Sponsor> $sponsors
     * @return list<ListSponsorDto>
     */
    public function toListDto(array $sponsors): array
    {
        return \array_map(
            fn(Sponsor $sponsor): ListSponsorDto => new ListSponsorDto(
                $sponsor->name,
                $sponsor->url,
                $sponsor->image->getPath($this->imagePath),
            ),
            $sponsors,
        );
    }

    public function toDetailDto(Sponsor $sponsor): SponsorDetailDto
    {
        return new SponsorDetailDto(
            $sponsor->name,
            $sponsor->url,
            $sponsor->image->getPath($this->imagePath),
            $sponsor
                ->seasons
                ->map(fn(Season $season): int => $season->id)
                ->toArray(),
        );
    }
}
