<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Cache\CacheItemPoolInterface;
use Ramsey\Uuid\Doctrine\UuidType;

class DoctrineConfig
{
    public Configuration $config;
    public Connection $connection;

    public function __construct(CacheItemPoolInterface $cache)
    {
        if (Type::hasType(UuidType::NAME) === false) {
            Type::addType(
                UuidType::NAME,
                UuidType::class,
            );
        }

        $this->config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../src/' . ($_ENV['PROJECT_NAME'] ?? 'None') . '/Entity'],
            isDevMode: $_ENV['DEBUG'] === 'true',
            cache: $cache,
        );

        $this->config->enableNativeLazyObjects(true);

        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => $_ENV['DATABASE_HOSTNAME'] ?? '',
            'port' => 3_306,
            'dbname' => $_ENV['DATABASE_NAME'] ?? '',
            'user' => $_ENV['DATABASE_USERNAME'] ?? '',
            'password' => $_ENV['DATABASE_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ], $this->config);
    }
}

return function (ContainerBuilder $containerBuilder): void {
    $containerBuilder->addDefinitions([
        EntityManager::class => function (Container $container) {

            $doctrineConfig = new DoctrineConfig($container->get('cache'));

            return new EntityManager(
                $doctrineConfig->connection,
                $doctrineConfig->config,
            );
        },
    ]);
};
