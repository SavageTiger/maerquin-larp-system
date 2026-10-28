<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

use Override;
use SvenHK\Maerquin\Status\Results\CpuStatus;
use SvenHK\Maerquin\Status\Results\MemoryStatus;
use SvenHK\Maerquin\Status\Results\Unavailable;

/**
 * Reads hardware information through sysctl(8) and swapinfo(8).
 *
 * FreeBSD has no /proc by default and no cgroups; jail resource limits (rctl) are not reported.
 */
class FreeBsdPlatform implements Platform
{
    private const string SWAPINFO_BINARY = '/usr/sbin/swapinfo';

    public function __construct(
        private readonly Sysctl $sysctl,
        private readonly CommandRunner $commandRunner,
        private readonly LoadAverageReader $loadAverageReader,
    ) {
    }

    #[Override]
    public function getCpuStatus(): CpuStatus|Unavailable
    {
        try {
            $values = $this->sysctl->read(['hw.ncpu', 'hw.model']);
            $cores = $this->sysctl->requireInt($values, 'hw.ncpu');

            if ($cores === 0) {
                throw SystemInfoNotAvailable::cpuCoresUnknown();
            }

            return new CpuStatus(
                model: $values['hw.model'] ?? '',
                cores: $cores,
                quota: 0.0,
                load: $this->loadAverageReader->read(),
            );
        } catch (SystemInfoNotAvailable $exception) {
            return $exception->toUnavailable();
        }
    }

    #[Override]
    public function getMemoryStatus(): MemoryStatus|Unavailable
    {
        try {
            $values = $this->sysctl->read([
                'hw.physmem',
                'hw.pagesize',
                'vm.stats.vm.v_free_count',
                'vm.stats.vm.v_inactive_count',
            ]);

            $total = $this->sysctl->requireInt($values, 'hw.physmem');

            if ($total === 0) {
                throw SystemInfoNotAvailable::memoryTotalUnknown();
            }

            // Free and inactive pages can be handed out without swapping, comparable to Linux' MemAvailable.
            $pageSize = $this->sysctl->requireInt($values, 'hw.pagesize');
            $available = $pageSize * (
                $this->sysctl->requireInt($values, 'vm.stats.vm.v_free_count')
                + $this->sysctl->requireInt($values, 'vm.stats.vm.v_inactive_count')
            );

            [$swapTotal, $swapUsed] = $this->getSwap();

            return new MemoryStatus(
                total: $total,
                available: min($available, $total),
                swapTotal: $swapTotal,
                swapFree: $swapTotal - $swapUsed,
                phpUsage: memory_get_usage(true),
                phpPeak: memory_get_peak_usage(true),
                container: new Unavailable('status.unavailable.no_container'),
            );
        } catch (SystemInfoNotAvailable $exception) {
            return $exception->toUnavailable();
        }
    }

    /**
     * Parses `swapinfo -k`. With several swap devices the last line is a "Total" line,
     * with one device it is that device; without swap there is only the header.
     *
     * @return array{0: int, 1: int} Total and used swap in bytes
     */
    private function getSwap(): array
    {
        try {
            $lines = array_values(array_filter(
                explode("\n", $this->commandRunner->run([self::SWAPINFO_BINARY, '-k'])),
                fn(string $line): bool => trim($line) !== '',
            ));
        } catch (SystemInfoNotAvailable) {
            return [0, 0];
        }

        if (count($lines) < 2) {
            return [0, 0];
        }

        $columns = preg_split('/\s+/', trim($lines[count($lines) - 1])) ?: [];

        if (count($columns) < 3 || ctype_digit($columns[1]) === false || ctype_digit($columns[2]) === false) {
            return [0, 0];
        }

        return [(int)$columns[1] * 1_024, (int)$columns[2] * 1_024];
    }
}
