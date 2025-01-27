<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class PlayersController extends Action
{
    public function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render($this->response, 'players.html.twig');
    }
}
