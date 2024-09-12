<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;

class HomeController extends Action
{
    use RedirectTo;

    public function action(): ResponseInterface
    {
        return $this->redirectTo('/login.html');
    }
}
