<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\Dotenv\Dotenv;

use function dirname;
use function file_exists;
use function method_exists;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    new Dotenv()->bootEnv(dirname(__DIR__) . '/.env.test');
}
