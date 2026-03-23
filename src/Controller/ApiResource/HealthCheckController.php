<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/healthcheck',
    name: self::ROUTE,
    methods: [Request::METHOD_GET],
)]
final class HealthCheckController extends AbstractController
{
    public const string ROUTE = 'health-check';

    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
            'timestamp' => new \DateTime()->format('c'),
            'application' => 'Vytrvalec',
            'version' => '1.0.0',
        ], Response::HTTP_OK);
    }
}
