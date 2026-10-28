<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider;

use Predis\ClientInterface;
use SvenHK\Maerquin\Status\Results\CacheStatus;
use SvenHK\Maerquin\Status\Results\Unavailable;
use Throwable;

class CacheStatusProvider
{
    public function __construct(
        private readonly ClientInterface $cacheClient,
    ) {
    }

    public function getStatus(): CacheStatus|Unavailable
    {
        try {
            $info = $this->flattenInfo((array)$this->cacheClient->info());

            return new CacheStatus(
                server: isset($info['valkey_version']) ? 'Valkey' : 'Redis',
                version: (string)($info['valkey_version'] ?? $info['redis_version'] ?? ''),
                uptime: (int)($info['uptime_in_seconds'] ?? 0),
                keys: (int)$this->cacheClient->dbsize(),
                usedMemory: (int)($info['used_memory'] ?? 0),
                peakMemory: (int)($info['used_memory_peak'] ?? 0),
                maxMemory: (int)($info['maxmemory'] ?? 0),
                hits: (int)($info['keyspace_hits'] ?? 0),
                misses: (int)($info['keyspace_misses'] ?? 0),
            );
        } catch (Throwable $exception) {
            return new Unavailable('status.unavailable.cache', [
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Predis returns INFO either flat or grouped per section ("Server", "Memory", ...),
     * depending on the server version.
     *
     * @param array<mixed> $info
     * @return array<string, mixed>
     */
    private function flattenInfo(array $info): array
    {
        $flat = [];

        foreach ($info as $key => $value) {
            if (is_array($value) && ctype_upper(substr((string)$key, 0, 1))) {
                $flat = array_merge($flat, $value);

                continue;
            }

            $flat[(string)$key] = $value;
        }

        return $flat;
    }
}
