<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

final readonly class ExtensionStatus
{
    private function __construct(
        public string $name,
        public bool $installed,
        public string $version,
    ) {
    }

    public static function installed(string $name, string $version): self
    {
        return new self($name, true, $version);
    }

    public static function missing(string $name): self
    {
        return new self($name, false, '');
    }
}
