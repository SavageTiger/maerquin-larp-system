<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use SvenHK\Maerquin\Status\Results\DatabaseStatus;
use SvenHK\Maerquin\Status\Results\Unavailable;
use Throwable;

class DatabaseStatusProvider
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getStatus(): DatabaseStatus | Unavailable
    {
        $connection = $this->entityManager->getConnection();
        $host = ScalarMap::fromArray($connection->getParams())->getString('host');

        try {
            $name = $connection->getDatabase() ?? '';
            $size = $this->getSize($connection, $name);

            return new DatabaseStatus(
                host: $host,
                name: $name,
                version: $connection->getServerVersion(),
                uptime: $this->getUptime($connection),
                tableCount: $size->getInt('table_count'),
                dataSize: $size->getInt('data_size'),
                indexSize: $size->getInt('index_size'),
            );
        } catch (Throwable $exception) {
            return new Unavailable('status.unavailable.database', [
                'host' => $host,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function getSize(Connection $connection, string $schema): ScalarMap
    {
        return ScalarMap::fromArray(
            $connection->createQueryBuilder()
                ->select(
                    'COUNT(*) AS table_count',
                    'COALESCE(SUM(tables.data_length), 0) AS data_size',
                    'COALESCE(SUM(tables.index_length), 0) AS index_size',
                )
                ->from('information_schema.tables', 'tables')
                ->where('tables.table_schema = :schema')
                ->setParameter('schema', $schema)
                ->fetchAssociative(),
        );
    }

    /**
     * @return int Uptime in seconds, 0 when performance_schema is disabled
     */
    private function getUptime(Connection $connection): int
    {
        try {
            return ScalarMap::fromArray(
                $connection->createQueryBuilder()
                    ->select('status.VARIABLE_VALUE AS uptime')
                    ->from('performance_schema.global_status', 'status')
                    ->where('status.VARIABLE_NAME = :variable')
                    ->setParameter('variable', 'Uptime')
                    ->fetchAssociative(),
            )->getInt('uptime');
        } catch (Throwable) {
            return 0;
        }
    }
}
