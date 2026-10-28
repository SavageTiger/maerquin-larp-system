<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder): void {
    $containerBuilder->addDefinitions([
        App\Application\Session\Session::class => DI\get(SvenHK\Maerquin\Session\Session::class),

        SvenHK\Maerquin\Translation\Translator::class => DI\autowire()
            ->constructorParameter('cache', DI\get('cache')),

        SvenHK\Maerquin\Status\Provider\Platform\Platform::class => DI\factory(
            [SvenHK\Maerquin\Status\Provider\Platform\PlatformFactory::class, 'create'],
        ),

        SvenHK\Maerquin\Status\Provider\CacheStatusProvider::class => DI\autowire()
            ->constructorParameter('cacheClient', DI\get('cache.client')),

        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);
};
