<?php

declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder): void {
    $valkeyHostname = (string)$_ENV['VALKEY_HOSTNAME'];

    $client = new Predis\Client(sprintf('tcp://%s', $valkeyHostname));
    $cache = new Symfony\Component\Cache\Adapter\RedisAdapter($client);

    $containerBuilder->addDefinitions([
        'cache' => $cache,
    ]);
};
