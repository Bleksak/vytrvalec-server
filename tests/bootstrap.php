<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\Dotenv\Dotenv;

use function dirname;

require dirname(__DIR__) . '/vendor/autoload.php';

new Dotenv()->bootEnv(dirname(__DIR__) . '/.env.test');
