<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider;

use SvenHK\Maerquin\Status\Results\ExtensionStatus;
use SvenHK\Maerquin\Status\Results\PhpStatus;

class PhpStatusProvider
{
    /**
     * Extensions the application needs at runtime, besides the ext-* entries in composer.json.
     */
    private const array RUNTIME_EXTENSIONS = [
        'ctype',
        'iconv',
        'mbstring',
        'openssl',
        'pdo',
        'pdo_mysql',
        'session',
        'zlib',
        'Zend OPcache',
    ];

    public function __construct(
        private readonly string $composerFile = __DIR__ . '/../../../../composer.json',
    ) {
    }

    public function getStatus(): PhpStatus
    {
        return new PhpStatus(
            version: PHP_VERSION,
            sapi: PHP_SAPI,
            memoryLimit: (string)ini_get('memory_limit'),
            extensions: array_map(
                fn(string $name): ExtensionStatus => $this->getExtension($name),
                $this->getRequiredExtensionNames(),
            ),
        );
    }

    private function getExtension(string $name): ExtensionStatus
    {
        if (extension_loaded($name) === false) {
            return ExtensionStatus::missing($name);
        }

        return ExtensionStatus::installed($name, (string)phpversion($name));
    }

    /**
     * @return list<string>
     */
    private function getRequiredExtensionNames(): array
    {
        $names = self::RUNTIME_EXTENSIONS;

        foreach ($this->getComposerRequirements() as $package) {
            if (str_starts_with($package, 'ext-')) {
                $names[] = substr($package, 4);
            }
        }

        $names = array_unique($names);
        natcasesort($names);

        return array_values($names);
    }

    /**
     * @return list<string>
     */
    private function getComposerRequirements(): array
    {
        if (is_file($this->composerFile) === false) {
            return [];
        }

        $composer = json_decode((string)file_get_contents($this->composerFile), true);

        if (is_array($composer) === false || is_array($composer['require'] ?? false) === false) {
            return [];
        }

        return array_map('strval', array_keys($composer['require']));
    }
}
