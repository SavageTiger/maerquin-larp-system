<?php

declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder): void {
    $valkeyHostname = (string)$_ENV['VALKEY_HOSTNAME'];
    $valkeyPassword = (string)$_ENV['VALKEY_PASSWORD'];

    $client = new Predis\Client(
        [
            'scheme' => 'tcp',
            'host' => $valkeyHostname,
            'password' => $valkeyPassword,
            'port' => 6_379,
        ],
    );
    $cache = new Symfony\Component\Cache\Adapter\RedisAdapter($client);

    $containerBuilder->addDefinitions([
        'cache' => $cache,
    ]);
};
