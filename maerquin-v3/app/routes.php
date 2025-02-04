<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use SvenHK\Maerquin\Controller\API\CharacterApiController;
use SvenHK\Maerquin\Controller\CharactersController;
use SvenHK\Maerquin\Controller\HomeController;
use SvenHK\Maerquin\Controller\LoginController;
use SvenHK\Maerquin\Controller\PlayersController;
use SvenHK\Maerquin\Controller\SkillsController;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->get('/', HomeController::class);
    $app->get('/home.html', HomeController::class);

    $app->get('/login.html', LoginController::class);
    $app->post('/login.html', LoginController::class);

    $app->get('/admin/characters.html', CharactersController::class);
    $app->get('/admin/characters/{characterId}.html', CharactersController::class);
    $app->get('/admin/characters/api', CharacterApiController::class);
    $app->get('/admin/characters/api/for-player/{playerId}', CharacterApiController::class);

    $app->get('/admin/players.html', PlayersController::class);
    $app->get('/admin/players/{userId}.html', PlayersController::class);
    $app->post('/admin/players/{userId}.html', PlayersController::class);

    $app->get('/admin/skills.html', SkillsController::class);
    $app->get('/admin/skills/{skillId}.html', SkillsController::class);
};
