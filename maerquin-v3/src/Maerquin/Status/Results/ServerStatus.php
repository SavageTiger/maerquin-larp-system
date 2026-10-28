<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

final readonly class ServerStatus
{
    public function __construct(
        public string $os,
        public string $hostname,
        public string $kernel,
        public string $kernelBuild,
        public string $architecture,
    ) {
    }
}
