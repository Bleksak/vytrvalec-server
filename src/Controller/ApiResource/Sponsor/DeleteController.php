<?php

declare(strict_types=1);

namespace App\Controller\ApiResource\Sponsor;

use App\Entity\Sponsor;
use App\Services\Sponsor\SponsorDeleteService;
use App\Utils\FeatureFlag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    '/api/sponsor/{sponsor}',
    name: self::ROUTE,
    methods: [Request::METHOD_DELETE],
)]
final class DeleteController extends AbstractController
{
    public const string ROUTE = 'api.sponsor.delete';

    public function __construct(
        private readonly SponsorDeleteService $sponsorDeleteService,
    ) {}

    #[IsGranted(FeatureFlag::ROLE_STAFF->value)]
    public function __invoke(Sponsor $sponsor): Response
    {
        $this->sponsorDeleteService->__invoke($sponsor);
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
