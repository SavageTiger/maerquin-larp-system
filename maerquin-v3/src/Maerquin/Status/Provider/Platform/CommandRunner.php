<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

/**
 * Runs a system command without a shell, so arguments are never interpreted.
 */
class CommandRunner
{
    /**
     * @param non-empty-list<string> $command Absolute path of the binary followed by its arguments
     * @param bool $ignoreExitCode Return the output even when the command exits with an error
     *
     * @throws SystemInfoNotAvailable
     */
    public function run(array $command, bool $ignoreExitCode = false): string
    {
        if (function_exists('proc_open') === false) {
            throw SystemInfoNotAvailable::commandsDisabled();
        }

        if (@is_executable($command[0]) === false) {
            throw SystemInfoNotAvailable::commandFailed($command[0]);
        }

        $process = @proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

        if (is_resource($process) === false) {
            throw SystemInfoNotAvailable::commandFailed($command[0]);
        }

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($output === false || ($exitCode !== 0 && $ignoreExitCode === false)) {
            throw SystemInfoNotAvailable::commandFailed($command[0]);
        }

        return $output;
    }
}
