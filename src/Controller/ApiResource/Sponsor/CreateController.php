<?php

declare(strict_types=1);

namespace App\Controller\ApiResource\Sponsor;

use App\Dto\Sponsor\Request\SponsorCreateDto;
use App\Services\Sponsor\SponsorCreateService;
use App\Services\Sponsor\SponsorDtoMapper;
use App\Utils\FeatureFlag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/sponsor', name: self::ROUTE, methods: [Request::METHOD_POST])]
final class CreateController extends AbstractController
{
    public const string ROUTE = 'api.sponsor.create';

    public function __construct(
        private readonly SponsorCreateService $sponsorCreateService,
        private readonly SponsorDtoMapper $sponsorDtoMapper,
    ) {}

    #[IsGranted(FeatureFlag::ROLE_STAFF->value)]
    public function __invoke(
        #[MapRequestPayload]
        SponsorCreateDto $dto,
    ): Response {
        $sponsor = $this->sponsorCreateService->__invoke($dto);
        return $this->json($this->sponsorDtoMapper->toDetailDto($sponsor));
    }
}
