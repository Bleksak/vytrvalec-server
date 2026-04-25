<?php

declare(strict_types=1);

namespace App\Controller\ApiResource\Sponsor;

use App\Repository\SponsorRepository;
use App\Services\Sponsor\SponsorDtoMapper;
use App\Utils\FeatureFlag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/sponsor', methods: [Request::METHOD_GET])]
final class ListController extends AbstractController
{
    public const string ROUTE = 'api.sponsor.list';

    public function __construct(
        public readonly SponsorRepository $sponsorRepository,
        public readonly SponsorDtoMapper $sponsorDtoConverter,
    ) {}

    #[IsGranted(FeatureFlag::ROLE_STAFF->value)]
    public function __invoke(): Response
    {
        return $this->json(
            $this->sponsorDtoConverter->toListDto(
                $this->sponsorRepository->findAll(),
            ),
        );
    }
}
