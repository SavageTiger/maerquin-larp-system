<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

use RuntimeException;
use SvenHK\Maerquin\Status\Results\Unavailable;

final class SystemInfoNotAvailable extends RuntimeException
{
    /**
     * @param array<string, string> $parameters
     */
    private function __construct(
        string $message,
        private readonly string $translationKey,
        private readonly array $parameters = [],
    ) {
        parent::__construct($message);
    }

    public static function fileNotReadable(string $path): self
    {
        return new self(
            sprintf('%s is not readable on this system.', $path),
            'status.unavailable.file_not_readable',
            ['path' => $path],
        );
    }

    public static function unexpectedFileContent(string $path): self
    {
        return new self(
            sprintf('%s has unexpected content.', $path),
            'status.unavailable.file_unexpected_content',
            ['path' => $path],
        );
    }

    public static function commandsDisabled(): self
    {
        return new self('proc_open() is disabled.', 'status.unavailable.commands_disabled');
    }

    public static function commandFailed(string $command): self
    {
        return new self(
            sprintf('Command %s could not be executed.', $command),
            'status.unavailable.command_failed',
            ['command' => $command],
        );
    }

    public static function sysctlMissing(string $name): self
    {
        return new self(
            sprintf('sysctl %s is not available.', $name),
            'status.unavailable.sysctl_missing',
            ['name' => $name],
        );
    }

    public static function cpuCoresUnknown(): self
    {
        return new self('Number of CPU cores could not be determined.', 'status.unavailable.cpu_cores');
    }

    public static function loadAverageUnavailable(): self
    {
        return new self('Load average is not available.', 'status.unavailable.cpu_load');
    }

    public static function memoryTotalUnknown(): self
    {
        return new self('Total memory could not be determined.', 'status.unavailable.memory_total');
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    public function toUnavailable(): Unavailable
    {
        return new Unavailable($this->translationKey, $this->parameters);
    }
}
