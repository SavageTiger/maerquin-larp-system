<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use SvenHK\Maerquin\Controller\HomeController;
use SvenHK\Maerquin\Controller\LoginController;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', HomeController::class);
    $app->get('/home.html', HomeController::class);

    $app->get('/login.html', LoginController::class);
    $app->post('/login.html', LoginController::class);
};
