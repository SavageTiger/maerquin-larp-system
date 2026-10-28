<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

/**
 * Sizes are in bytes, uptime in seconds.
 */
final readonly class DatabaseStatus implements StatusSection
{
    public function __construct(
        public string $host,
        public string $name,
        public string $version,
        public int $uptime,
        public int $tableCount,
        public int $dataSize,
        public int $indexSize,
    ) {
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function hasUptime(): bool
    {
        return $this->uptime > 0;
    }

    public function totalSize(): int
    {
        return $this->dataSize + $this->indexSize;
    }
}
