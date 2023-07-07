<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class JWTLogout
{
    /**
     * @param LogoutEvent $logoutEvent
     * @return void
     */
    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {
        $response = $logoutEvent->getResponse();

        $response->headers->clearCookie('jwt');
        $response->headers->set('Content-Type', 'text/json');
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent(json_encode([
            'success' => true
        ]));
    }
}