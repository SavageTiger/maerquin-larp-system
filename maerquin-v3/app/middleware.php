<?php

declare(strict_types=1);

use App\Application\Middleware\Firewall;
use App\Application\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {
//    $twig = Twig::create(__DIR__ . '/../src/.../Templates', ['cache' => false]);
//    //    $twig = Twig::create(__DIR__ . '/../src/.../Templates', ['cache' => __DIR__ . '/../var/cache/twig']);
//    $twig->addExtension(new Extensions($app->getContainer()->get(Session::class)));
//    $twig->addExtension(new AdminExtension());

    $app->add(Firewall::class);
    $app->add(SessionMiddleware::class);
//    $app->add(TwigMiddleware::create($app, $twig));
};
