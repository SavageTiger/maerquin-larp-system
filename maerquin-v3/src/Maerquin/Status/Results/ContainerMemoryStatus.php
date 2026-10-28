<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

/**
 * Memory of the cgroup the application runs in. Sizes are in bytes.
 */
final readonly class ContainerMemoryStatus implements StatusSection
{
    /**
     * @param int $limit 0 when the container has no memory limit
     */
    public function __construct(
        public int $usage,
        public int $limit,
    ) {
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function isLimited(): bool
    {
        return $this->limit > 0;
    }
}
