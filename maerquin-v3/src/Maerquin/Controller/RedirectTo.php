<?php

namespace SvenHK\Maerquin\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

trait RedirectTo
{
    public function redirectTo(string $path): ResponseInterface
    {
        $headers = new Headers([
            'Location' => $path,
        ]);

        return new Response(302, $headers);
    }
}
