<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Symfony\Component\Mailer;
use Symfony\Component\Mailer\Transport;

class MailerFactory
{
    public static function create() : Mailer\Mailer
    {
        $dsn = '...';
        $transport = Transport::fromDsn($dsn);

        return new Mailer\Mailer($transport);
    }
}

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        Mailer\Mailer::class => function () {
            return MailerFactory::create();
        },
    ]);
};
