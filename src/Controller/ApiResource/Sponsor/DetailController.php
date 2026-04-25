<?php

declare(strict_types=1);

namespace App\Controller\ApiResource\Sponsor;

use App\Repository\SponsorRepository;
use App\Services\Sponsor\SponsorDtoMapper;
use App\Utils\FeatureFlag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    '/api/sponsor/{sponsor}',
    name: self::ROUTE,
    methods: [Request::METHOD_GET],
)]
final class DetailController extends AbstractController
{
    public const string ROUTE = 'api.sponsor.detail';

    public function __construct(
        private readonly SponsorRepository $sponsorRepository,
        private readonly SponsorDtoMapper $sponsorDtoMapper,
    ) {}

    #[IsGranted(FeatureFlag::ROLE_STAFF->value)]
    public function __invoke(int $sponsor): Response
    {
        $sponsor = $this->sponsorRepository->findOneWithSeasons($sponsor);

        if ($sponsor === null) {
            throw new NotFoundHttpException('Sponsor not found');
        }

        return $this->json($this->sponsorDtoMapper->toDetailDto($sponsor));
    }
}
