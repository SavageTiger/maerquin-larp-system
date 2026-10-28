<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

final readonly class Unavailable implements StatusSection
{
    /**
     * @param string $translationKey Explains why the section is unavailable
     * @param array<string, string> $parameters Placeholders for the translation
     */
    public function __construct(
        public string $translationKey,
        public array $parameters = [],
    ) {
    }

    public function isAvailable(): bool
    {
        return false;
    }
}
