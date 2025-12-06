<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rules', name: self::ROUTE, methods: [Request::METHOD_GET])]
final class RulesController extends AbstractController
{
    public const string ROUTE = 'rules';

    public function __invoke(): Response
    {
        return $this->render('rules.html.twig');
    }
}
