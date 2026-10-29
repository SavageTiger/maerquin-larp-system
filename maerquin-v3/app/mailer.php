<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Symfony\Component\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class MailerFactory
{
    public static function create(): Mailer\Mailer
    {
        $transport = Transport::fromDsn($_ENV['EMAIL_SERVER_DSN']);

        if ($transport instanceof EsmtpTransport) {
            $stream = $transport->getStream();

            if ($stream instanceof SocketStream) {
                $options = $stream->getStreamOptions();
                $options['ssl']['verify_peer_name'] = false;

                $stream->setStreamOptions($options);
            }
        }

        return new Mailer\Mailer($transport);
    }
}

return function (ContainerBuilder $containerBuilder): void {
    $containerBuilder->addDefinitions([
        Mailer\Mailer::class => function () {
            return MailerFactory::create();
        },
    ]);
};
