<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/results', name: self::ROUTE, methods: [Request::METHOD_GET])]
final class ResultsController extends AbstractController
{
    public const string ROUTE = 'results';

    public function __construct() {}

    public function __invoke(): Response
    {
        return $this->render('results.html.twig');
    }
}
