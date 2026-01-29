<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Dto\Mobile\Response\MobileVersionResponseDto;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag('Mobile')]
final class MobileController extends AbstractController
{
    public function __construct() {}

    #[OA\Get(description: 'Retrieve current mobile app versions', responses: [
        new OA\Response(
            description: 'Version object',
            response: Response::HTTP_OK,
            content: new OA\JsonContent(
                ref: new Model(type: MobileVersionResponseDto::class),
            ),
        ),
    ])]
    #[Route(
        '/api/mobile/version',
        name: 'app_mobile_version',
        methods: ['GET'],
    )]
    public function version(): Response
    {
        return $this->json(new MobileVersionResponseDto(
            android: '1.2.0',
            ios: '1.2.0',
        ));
    }
}
