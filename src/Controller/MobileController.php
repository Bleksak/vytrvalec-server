<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MobileController extends AbstractController
{
    #[Route('/mobile/version', name: 'app_mobile_version', methods: ['GET'])]
    public function version(): Response
    {
        return $this->json([
            'android' => '1.2.0',
            'ios' => '1.2.0',
        ]);
    }
}
