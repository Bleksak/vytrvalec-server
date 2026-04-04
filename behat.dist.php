<?php

declare(strict_types=1);

use App\Tests\Behat\AuthContext;
use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;

return new Config()->withProfile(new Profile('default')
    ->withSuite(new Suite('suite')->addContext(AuthContext::class))
    ->withExtension(new Extension(SymfonyExtension::class, [
        'bootstrap' => __DIR__ . '/tests/bootstrap.php',
        'kernel' => [
            'environment' => 'test',
        ],
    ])));
