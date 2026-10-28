<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

final readonly class CpuStatus implements StatusSection
{
    /**
     * @param string $model Empty when the CPU does not report a model name (common on ARM)
     * @param float $quota CPU quota of the container, 0 when unlimited
     */
    public function __construct(
        public string $model,
        public int $cores,
        public float $quota,
        public LoadAverage $load,
    ) {
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function hasModel(): bool
    {
        return $this->model !== '';
    }

    public function isLimited(): bool
    {
        return $this->quota > 0;
    }

    /**
     * Number of CPUs this process can actually use.
     */
    public function capacity(): float
    {
        return $this->isLimited() ? $this->quota : (float)$this->cores;
    }

    public function loadPercentage(): int
    {
        return (int)min(100, round($this->load->oneMinute / $this->capacity() * 100));
    }
}
