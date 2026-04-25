<?php

declare(strict_types=1);

namespace App\Services\Sponsor;

use App\Entity\Sponsor;
use App\Exceptions\Sponsor\SponsorCannotBeDeletedException;
use App\Repository\SponsorRepository;

final readonly class SponsorDeleteService
{
    public function __construct(
        private SponsorRepository $sponsorRepository,
    ) {}

    /**
     * @throws SponsorCannotBeDeletedException
     */
    public function __invoke(Sponsor $sponsor): void
    {
        if ($sponsor->seasons->count() > 0) {
            throw new SponsorCannotBeDeletedException();
        }

        $this->sponsorRepository->remove($sponsor, true);
    }
}
