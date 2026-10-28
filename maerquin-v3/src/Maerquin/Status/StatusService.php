<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status;

use SvenHK\Maerquin\Status\Provider\CacheStatusProvider;
use SvenHK\Maerquin\Status\Provider\DatabaseStatusProvider;
use SvenHK\Maerquin\Status\Provider\PhpStatusProvider;
use SvenHK\Maerquin\Status\Provider\Platform\Platform;
use SvenHK\Maerquin\Status\Provider\ServerStatusProvider;
use SvenHK\Maerquin\Status\Results\SystemStatus;

class StatusService
{
    public function __construct(
        private readonly PhpStatusProvider $phpStatusProvider,
        private readonly ServerStatusProvider $serverStatusProvider,
        private readonly Platform $platform,
        private readonly DatabaseStatusProvider $databaseStatusProvider,
        private readonly CacheStatusProvider $cacheStatusProvider,
    ) {
    }

    public function getStatus(): SystemStatus
    {
        return new SystemStatus(
            php: $this->phpStatusProvider->getStatus(),
            server: $this->serverStatusProvider->getStatus(),
            cpu: $this->platform->getCpuStatus(),
            memory: $this->platform->getMemoryStatus(),
            database: $this->databaseStatusProvider->getStatus(),
            cache: $this->cacheStatusProvider->getStatus(),
        );
    }
}
