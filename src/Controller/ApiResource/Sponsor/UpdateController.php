<?php

declare(strict_types=1);

namespace App\Controller\ApiResource\Sponsor;

use App\Dto\Sponsor\Request\SponsorUpdateDto;
use App\Entity\Sponsor;
use App\Services\Sponsor\SponsorDtoMapper;
use App\Services\Sponsor\SponsorUpdateService;
use App\Utils\FeatureFlag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    '/api/sponsor/{sponsor}',
    name: self::ROUTE,
    methods: [Request::METHOD_PATCH],
)]
final class UpdateController extends AbstractController
{
    public const string ROUTE = 'api.sponsor.update';

    public function __construct(
        private readonly SponsorUpdateService $sponsorUpdateService,
        private readonly SponsorDtoMapper $sponsorDtoMapper,
    ) {}

    #[IsGranted(FeatureFlag::ROLE_STAFF->value)]
    public function __invoke(
        Sponsor $sponsor,
        #[MapRequestPayload]
        SponsorUpdateDto $dto,
    ): Response {
        $sponsor = $this->sponsorUpdateService->__invoke($sponsor, $dto);
        return $this->json($this->sponsorDtoMapper->toDetailDto($sponsor));
    }
}
