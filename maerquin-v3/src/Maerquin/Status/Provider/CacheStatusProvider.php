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

    public function getStatus(): CacheStatus | Unavailable
    {
        try {
            $info = $this->getInfo();
            $isValkey = $info->has('valkey_version');

            return new CacheStatus(
                server: $isValkey ? 'Valkey' : 'Redis',
                version: $info->getString($isValkey ? 'valkey_version' : 'redis_version'),
                uptime: $info->getInt('uptime_in_seconds'),
                keys: $this->getKeyCount(),
                usedMemory: $info->getInt('used_memory'),
                peakMemory: $info->getInt('used_memory_peak'),
                maxMemory: $info->getInt('maxmemory'),
                hits: $info->getInt('keyspace_hits'),
                misses: $info->getInt('keyspace_misses'),
            );
        } catch (Throwable $exception) {
            return new Unavailable('status.unavailable.cache', [
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Predis returns INFO either flat or grouped per section ("Server", "Memory", ...),
     * depending on the server version; the sections are merged into one map.
     */
    private function getInfo(): ScalarMap
    {
        $info = $this->cacheClient->info();

        if (is_array($info) === false) {
            return ScalarMap::fromArray([]);
        }

        $flat = [];

        foreach ($info as $key => $value) {
            if (is_array($value)) {
                $flat = array_merge($flat, $value);

                continue;
            }

            $flat[$key] = $value;
        }

        return ScalarMap::fromArray($flat);
    }

    private function getKeyCount(): int
    {
        $keys = $this->cacheClient->dbsize();

        return is_int($keys) ? $keys : 0;
    }
}
