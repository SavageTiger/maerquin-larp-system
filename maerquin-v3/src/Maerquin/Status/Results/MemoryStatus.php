<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

/**
 * All sizes are in bytes.
 */
final readonly class MemoryStatus implements StatusSection
{
    public function __construct(
        public int $total,
        public int $available,
        public int $swapTotal,
        public int $swapFree,
        public int $phpUsage,
        public int $phpPeak,
        public ContainerMemoryStatus | Unavailable $container,
    ) {
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function used(): int
    {
        return max(0, $this->total - $this->available);
    }

    public function usedPercentage(): int
    {
        return (int)round($this->used() / $this->total * 100);
    }

    public function hasSwap(): bool
    {
        return $this->swapTotal > 0;
    }

    public function swapUsed(): int
    {
        return max(0, $this->swapTotal - $this->swapFree);
    }
}
