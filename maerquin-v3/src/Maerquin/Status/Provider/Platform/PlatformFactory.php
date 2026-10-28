<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

class PlatformFactory
{
    public function __construct(
        private readonly LinuxPlatform $linuxPlatform,
        private readonly FreeBsdPlatform $freeBsdPlatform,
    ) {
    }

    public function create(): Platform
    {
        return match (PHP_OS) {
            'Linux' => $this->linuxPlatform,
            'FreeBSD' => $this->freeBsdPlatform,
            default => new UnsupportedPlatform(PHP_OS),
        };
    }
}
