<?php

declare(strict_types=1);

namespace App\Services\Sponsor;

use App\Dto\Sponsor\Request\SponsorUpdateDto;
use App\Entity\Sponsor;
use App\Repository\ImageRepository;
use App\Repository\SponsorRepository;
use thiagoalessio\TesseractOCR\ImageNotFoundException;

final readonly class SponsorUpdateService
{
    public function __construct(
        private SponsorRepository $sponsorRepository,
        private ImageRepository $imageRepository,
    ) {}

    /**
     * @throws ImageNotFoundException
     */
    public function __invoke(Sponsor $sponsor, SponsorUpdateDto $dto): Sponsor
    {
        if ($dto->name !== null) {
            $sponsor->name = $dto->name;
        }

        if ($dto->url !== null) {
            $sponsor->url = $dto->url;
        }

        if ($dto->image !== null) {
            $image = $this->imageRepository->find($dto->image);

            if ($image === null) {
                throw new ImageNotFoundException();
            }

            $sponsor->image = $image;
        }

        $this->sponsorRepository->save($sponsor, true);

        return $sponsor;
    }
}
