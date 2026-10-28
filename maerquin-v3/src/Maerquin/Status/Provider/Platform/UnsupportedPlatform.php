<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

use Override;
use SvenHK\Maerquin\Status\Results\CpuStatus;
use SvenHK\Maerquin\Status\Results\MemoryStatus;
use SvenHK\Maerquin\Status\Results\Unavailable;

final readonly class UnsupportedPlatform implements Platform
{
    public function __construct(
        private string $operatingSystem,
    ) {
    }

    #[Override]
    public function getCpuStatus(): CpuStatus|Unavailable
    {
        return $this->unavailable();
    }

    #[Override]
    public function getMemoryStatus(): MemoryStatus|Unavailable
    {
        return $this->unavailable();
    }

    private function unavailable(): Unavailable
    {
        return new Unavailable('status.unavailable.unsupported_os', ['os' => $this->operatingSystem]);
    }
}
