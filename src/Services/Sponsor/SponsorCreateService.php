<?php

declare(strict_types=1);

namespace App\Services\Sponsor;

use App\Dto\Sponsor\Request\SponsorCreateDto;
use App\Entity\Sponsor;
use App\Repository\ImageRepository;
use App\Repository\SponsorRepository;
use thiagoalessio\TesseractOCR\ImageNotFoundException;

final readonly class SponsorCreateService
{
    public function __construct(
        private SponsorRepository $sponsorRepository,
        private ImageRepository $imageRepository,
    ) {}

    /**
     * @throws ImageNotFoundException
     */
    public function __invoke(SponsorCreateDto $dto): Sponsor
    {
        $image = $this->imageRepository->find($dto->image);

        if ($image === null) {
            throw new ImageNotFoundException();
        }

        $sponsor = new Sponsor($dto->name, $dto->url, $image);
        $this->sponsorRepository->save($sponsor, true);

        return $sponsor;
    }
}
