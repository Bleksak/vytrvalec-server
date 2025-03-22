<?php

declare(strict_types=1);

namespace Tests;

pest()->extend(TestCase::class);

shell_exec('php bin/console doctrine:database:drop --env=test --force');
shell_exec('php bin/console doctrine:database:create --env=test');
shell_exec('php bin/console doctrine:migrations:migrate --env=test --no-interaction');
shell_exec('php bin/console doctrine:fixtures:load --env=test --no-interaction');
