<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

final readonly class PhpStatus
{
    /**
     * @param list<ExtensionStatus> $extensions
     */
    public function __construct(
        public string $version,
        public string $sapi,
        public string $memoryLimit,
        public array $extensions,
    ) {
    }
}
