<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

use SvenHK\Maerquin\Exception\Status\SystemInfoNotAvailableException;

/**
 * Reads kernel / cgroup files (/proc, /sys), which do not exist on every system.
 */
class SystemFileReader
{
    /**
     * @throws SystemInfoNotAvailableException
     */
    public function read(string $path): string
    {
        if (@is_readable($path) === false) {
            throw SystemInfoNotAvailableException::fileNotReadable($path);
        }

        $content = @file_get_contents($path);

        if ($content === false) {
            throw SystemInfoNotAvailableException::fileNotReadable($path);
        }

        return $content;
    }

    /**
     * Reads a cgroup limit file, where "max" means unlimited.
     *
     * @throws SystemInfoNotAvailableException
     *
     * @return int The limit, 0 when unlimited
     */
    public function readLimit(string $path): int
    {
        $content = trim($this->read($path));

        if ($content === 'max') {
            return 0;
        }

        return $this->toInt($path, $content);
    }

    /**
     * @throws SystemInfoNotAvailableException
     */
    public function readInt(string $path): int
    {
        return $this->toInt($path, trim($this->read($path)));
    }

    private function toInt(string $path, string $content): int
    {
        if (ctype_digit($content) === false) {
            throw SystemInfoNotAvailableException::unexpectedFileContent($path);
        }

        return (int)$content;
    }
}
