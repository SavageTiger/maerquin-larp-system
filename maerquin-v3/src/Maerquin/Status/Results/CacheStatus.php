<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

/**
 * Memory is in bytes, uptime in seconds.
 */
final readonly class CacheStatus implements StatusSection
{
    /**
     * @param int $maxMemory 0 when no memory limit is configured (same convention as Valkey/Redis)
     */
    public function __construct(
        public string $server,
        public string $version,
        public int $uptime,
        public int $keys,
        public int $usedMemory,
        public int $peakMemory,
        public int $maxMemory,
        public int $hits,
        public int $misses,
    ) {
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function isLimited(): bool
    {
        return $this->maxMemory > 0;
    }

    public function lookups(): int
    {
        return $this->hits + $this->misses;
    }

    /**
     * Percentage of key lookups that were a hit, 0 when there were no lookups yet.
     */
    public function hitRate(): float
    {
        if ($this->lookups() === 0) {
            return 0.0;
        }

        return $this->hits / $this->lookups() * 100;
    }
}
