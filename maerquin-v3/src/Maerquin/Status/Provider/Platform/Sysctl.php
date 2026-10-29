<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

use SvenHK\Maerquin\Exception\Status\SystemInfoNotAvailableException;

/**
 * Reads kernel state through sysctl(8), as used on FreeBSD.
 */
class Sysctl
{
    private const string BINARY = '/sbin/sysctl';

    public function __construct(
        private readonly CommandRunner $commandRunner,
    ) {
    }

    /**
     * Reads several values with a single sysctl call. Unknown names are left out of the result.
     *
     * @param non-empty-list<string> $names
     *
     * @throws SystemInfoNotAvailableException
     *
     * @return array<string, string>
     */
    public function read(array $names): array
    {
        // -e prints "name=value"; sysctl exits non-zero when one of the names is unknown,
        // but still prints the others.
        $output = $this->commandRunner->run([self::BINARY, '-e', ...$names], ignoreExitCode: true);

        $values = [];

        foreach (explode("\n", $output) as $line) {
            if (str_contains($line, '=') === false) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $values[trim($name)] = trim($value);
        }

        return $values;
    }

    /**
     * @param array<string, string> $values Result of read()
     *
     * @throws SystemInfoNotAvailableException
     */
    public function requireInt(array $values, string $name): int
    {
        $value = $values[$name] ?? '';

        if (ctype_digit($value) === false) {
            throw SystemInfoNotAvailableException::sysctlMissing($name);
        }

        return (int)$value;
    }
}
