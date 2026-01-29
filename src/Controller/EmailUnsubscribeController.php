<?php

declare(strict_types=1);

namespace App\Controller;

use App\Action\UserActions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/email-unsubscribe/{emailUnsubscribeHash}',
    self::ROUTE,
    methods: [Request::METHOD_GET, Request::METHOD_POST],
)]
final class EmailUnsubscribeController extends AbstractController
{
    const string ROUTE = 'user:email-unsubscribe';

    public function __construct(
        private readonly UserActions $userActions,
    ) {}

    public function __invoke(string $emailUnsubscribeHash): Response
    {
        if (!$this->userActions->disableMailing($emailUnsubscribeHash)) {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute(IndexController::ROUTE);
    }
}
