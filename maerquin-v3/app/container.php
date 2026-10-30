<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$dotenv->required([
    'PROJECT_NAME',
    'DATABASE_HOSTNAME',
    'DATABASE_NAME',
    'DATABASE_USERNAME',
    'DATABASE_PASSWORD',
    'VALKEY_HOSTNAME',
    'EMAIL_SERVER_DSN',
])->notEmpty();
$dotenv->required('DEBUG')->isBoolean();
$dotenv->required('VALKEY_PASSWORD');

date_default_timezone_set('Europe/Amsterdam');

$containerBuilder = new ContainerBuilder();

foreach (['cache', 'settings', 'dependencies', 'repositories', 'doctrine', 'mailer'] as $definitions) {
    (require __DIR__ . '/' . $definitions . '.php')($containerBuilder);
}

return $containerBuilder->build();
