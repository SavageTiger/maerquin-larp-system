<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

trait RedirectTo
{
    public function redirectTo(
        string $path,
        null | string $rememberMeCookie = null,
    ): ResponseInterface {
        $headers = new Headers([
            'Location' => $path,
        ]);

        if ($rememberMeCookie !== null) {
            $headers->addHeader(
                'Set-Cookie',
                sprintf(
                    'RMT=%s; Max-Age=%d; Path=/; SameSite=Lax',
                    rawurlencode($rememberMeCookie),
                    60 * 60 * 24 * 30,
                ),
            );
        }

        return new Response(302, $headers);
    }
}
