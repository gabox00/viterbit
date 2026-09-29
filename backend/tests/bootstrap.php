<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

foreach (['doctrine:database:create --if-not-exists', 'doctrine:migrations:migrate --no-interaction'] as $command) {
    passthru(sprintf('php %s/bin/console %s --env=test --quiet', escapeshellarg(dirname(__DIR__)), $command), $exitCode);
    if (0 !== $exitCode) {
        exit($exitCode);
    }
}
