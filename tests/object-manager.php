<?php

declare(strict_types=1);

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

new Dotenv()->bootEnv(__DIR__ . '/../.env');

$kernel = new Kernel(
    $_SERVER['APP_ENV'] ?? 'test',
    $_SERVER['APP_DEBUG'] ?? false,
);
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine') ?? throw new \RuntimeException(
    'Failed to get doctrine',
);
assert($entityManager instanceof EntityManagerInterface);
return $entityManager;
