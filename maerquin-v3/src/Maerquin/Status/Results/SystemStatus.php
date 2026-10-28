<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

final readonly class SystemStatus
{
    public function __construct(
        public PhpStatus $php,
        public ServerStatus $server,
        public CpuStatus | Unavailable $cpu,
        public MemoryStatus | Unavailable $memory,
        public DatabaseStatus | Unavailable $database,
        public CacheStatus | Unavailable $cache,
    ) {
    }
}
