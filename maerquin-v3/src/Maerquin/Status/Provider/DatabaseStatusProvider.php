<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider;

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

    public function getStatus(): DatabaseStatus|Unavailable
    {
        $connection = $this->entityManager->getConnection();
        $host = (string)($connection->getParams()['host'] ?? '');

        try {
            $uptime = $connection->fetchAssociative("SHOW GLOBAL STATUS LIKE 'Uptime'") ?: [];
            $size = $connection->fetchAssociative(
                'SELECT
                    COALESCE(SUM(data_length), 0) AS data_size,
                    COALESCE(SUM(index_length), 0) AS index_size,
                    COUNT(*) AS table_count
                FROM information_schema.tables
                WHERE table_schema = DATABASE()',
            ) ?: [];

            return new DatabaseStatus(
                host: $host,
                name: (string)$connection->getDatabase(),
                version: (string)$connection->fetchOne('SELECT VERSION()'),
                uptime: (int)($uptime['Value'] ?? 0),
                tableCount: (int)($size['table_count'] ?? 0),
                dataSize: (int)($size['data_size'] ?? 0),
                indexSize: (int)($size['index_size'] ?? 0),
            );
        } catch (Throwable $exception) {
            return new Unavailable('status.unavailable.database', [
                'host' => $host,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
