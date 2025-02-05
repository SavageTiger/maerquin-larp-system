<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Session\Session;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class Firewall implements Middleware
{
    public function __construct(private Session $session)
    {
    }

    public function process(Request $request, RequestHandler $handler) : Response
    {
        $uri = $request->getUri();

        if ($uri->getPath() === '/admin' || str_contains($uri->getPath(), '/admin/')) {
            if ($this->session->getUser()->isAnonymous() === true) {
                throw new Exception('Access denied');
            }

            if ($this->session->getUser()->isAdmin() === false) {
                throw new Exception('Access denied');
            }
        }

        return $handler->handle($request);
    }
}
