<?php

declare(strict_types=1);

use App\Application\Middleware\Firewall;
use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app): void {
    if ($_ENV['DEBUG'] === 'true') {
        $twig = Twig::create(__DIR__ . '/../src/' . $_ENV['PROJECT_NAME'] . '/Templates', ['cache' => false]);
    } else {
        $twig = Twig::create(__DIR__ . '/../src/' . $_ENV['PROJECT_NAME'] . '/Templates', ['cache' => __DIR__ . '/../var/cache/twig']);
    }

    $app->add(Firewall::class);
    $app->add(SessionMiddleware::class);
    $app->add(TwigMiddleware::create($app, $twig));
};
