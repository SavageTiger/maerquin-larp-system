<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

/**
 * Reads kernel / cgroup files (/proc, /sys), which do not exist on every system.
 */
class SystemFileReader
{
    /**
     * @throws SystemInfoNotAvailable
     */
    public function read(string $path): string
    {
        if (@is_readable($path) === false) {
            throw SystemInfoNotAvailable::fileNotReadable($path);
        }

        $content = @file_get_contents($path);

        if ($content === false) {
            throw SystemInfoNotAvailable::fileNotReadable($path);
        }

        return $content;
    }

    /**
     * Reads a cgroup limit file, where "max" means unlimited.
     *
     * @return int The limit, 0 when unlimited
     * @throws SystemInfoNotAvailable
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
     * @throws SystemInfoNotAvailable
     */
    public function readInt(string $path): int
    {
        return $this->toInt($path, trim($this->read($path)));
    }

    private function toInt(string $path, string $content): int
    {
        if (ctype_digit($content) === false) {
            throw SystemInfoNotAvailable::unexpectedFileContent($path);
        }

        return (int)$content;
    }
}
