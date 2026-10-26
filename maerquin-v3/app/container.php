<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();

$containerBuilder = new ContainerBuilder();

foreach (['cache', 'settings', 'dependencies', 'repositories', 'doctrine', 'mailer'] as $definitions) {
    (require __DIR__ . '/' . $definitions . '.php')($containerBuilder);
}

return $containerBuilder->build();
