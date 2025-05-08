<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class JWTLogout
{
    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {
        $response = $logoutEvent->getResponse();

        if (!$response) {
            return;
        }

        $response->headers->clearCookie('jwt');
        $response->headers->set('Content-Type', 'text/json');
        $response->setStatusCode(Response::HTTP_OK);

        $responseData = json_encode([
            'success' => true,
        ]);

        $response->setContent($responseData === false ? null : $responseData);
    }
}
