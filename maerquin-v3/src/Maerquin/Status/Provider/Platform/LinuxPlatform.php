<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

use Override;
use SvenHK\Maerquin\Exception\Status\SystemInfoNotAvailableException;
use SvenHK\Maerquin\Status\Results\ContainerMemoryStatus;
use SvenHK\Maerquin\Status\Results\CpuStatus;
use SvenHK\Maerquin\Status\Results\MemoryStatus;
use SvenHK\Maerquin\Status\Results\Unavailable;

/**
 * Reads hardware information from /proc and cgroup v2 (/sys/fs/cgroup).
 */
class LinuxPlatform implements Platform
{
    public function __construct(
        private readonly SystemFileReader $fileReader,
        private readonly LoadAverageReader $loadAverageReader,
    ) {
    }

    #[Override]
    public function getCpuStatus(): CpuStatus | Unavailable
    {
        try {
            $cpuInfo = $this->fileReader->read('/proc/cpuinfo');
            $cores = (int)preg_match_all('/^processor\s*:/m', $cpuInfo);

            if ($cores === 0) {
                throw SystemInfoNotAvailableException::cpuCoresUnknown();
            }

            return new CpuStatus(
                model: $this->parseModel($cpuInfo),
                cores: $cores,
                quota: $this->getContainerQuota(),
                load: $this->loadAverageReader->read(),
            );
        } catch (SystemInfoNotAvailableException $exception) {
            return $exception->toUnavailable();
        }
    }

    #[Override]
    public function getMemoryStatus(): MemoryStatus | Unavailable
    {
        try {
            $memInfo = $this->parseMemInfo($this->fileReader->read('/proc/meminfo'));

            if (($memInfo['MemTotal'] ?? 0) === 0) {
                throw SystemInfoNotAvailableException::memoryTotalUnknown();
            }

            return new MemoryStatus(
                total: $memInfo['MemTotal'],
                available: $memInfo['MemAvailable'] ?? $memInfo['MemFree'] ?? 0,
                swapTotal: $memInfo['SwapTotal'] ?? 0,
                swapFree: $memInfo['SwapFree'] ?? 0,
                phpUsage: memory_get_usage(true),
                phpPeak: memory_get_peak_usage(true),
                container: $this->getContainerMemoryStatus(),
            );
        } catch (SystemInfoNotAvailableException $exception) {
            return $exception->toUnavailable();
        }
    }

    private function parseModel(string $cpuInfo): string
    {
        if (preg_match('/^(?:model name|Hardware|cpu model)\s*:\s*(.+)$/mi', $cpuInfo, $matches) !== 1) {
            return '';
        }

        return trim($matches[1]);
    }

    /**
     * CPU quota of the cgroup, e.g. "200000 100000" means 2 CPUs, "max 100000" means unlimited.
     *
     * @return float The quota in CPUs, 0 when unlimited or not running in a cgroup
     */
    private function getContainerQuota(): float
    {
        try {
            $cpuMax = trim($this->fileReader->read('/sys/fs/cgroup/cpu.max'));
        } catch (SystemInfoNotAvailableException) {
            return 0.0;
        }

        if (preg_match('/^(\d+)\s+(\d+)$/', $cpuMax, $matches) !== 1 || (int)$matches[2] === 0) {
            return 0.0;
        }

        return round((int)$matches[1] / (int)$matches[2], 2);
    }

    private function getContainerMemoryStatus(): ContainerMemoryStatus | Unavailable
    {
        try {
            return new ContainerMemoryStatus(
                usage: $this->fileReader->readInt('/sys/fs/cgroup/memory.current'),
                limit: $this->fileReader->readLimit('/sys/fs/cgroup/memory.max'),
            );
        } catch (SystemInfoNotAvailableException $exception) {
            return $exception->toUnavailable();
        }
    }

    /**
     * @return array<string, int> Values in bytes, keyed by /proc/meminfo field name
     */
    private function parseMemInfo(string $content): array
    {
        $memInfo = [];

        foreach (explode("\n", $content) as $line) {
            if (preg_match('/^(\w+):\s+(\d+)\s*kB/', $line, $matches) === 1) {
                $memInfo[$matches[1]] = (int)$matches[2] * 1_024;
            }
        }

        return $memInfo;
    }
}
