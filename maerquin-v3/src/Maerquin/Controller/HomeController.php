<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Session\Session;

class HomeController extends Action
{
    use RedirectTo;

    public function __construct(private Session $session)
    {
    }

    public function action(): ResponseInterface
    {
        if ($this->session->read('userId', false) === false) {
            return $this->redirectTo('/login.html');
        }

        $view = Twig::fromRequest($this->request);

        return $view->render($this->response, 'home.html.twig');
    }
}
