<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Translation;

use RuntimeException;

final class Translator
{
    private const string DEFAULT_LOCALE = 'nl';

    /**
     * @var array<string, string>|null
     */
    private null | array $messages = null;

    public function __construct(
        private readonly string $locale = self::DEFAULT_LOCALE,
        private readonly string $translationsPath = __DIR__ . '/../Translations',
    ) {
    }

    /**
     * Replaces `%name%` placeholders with the given parameters.
     *
     * @param array<string, string|int|float> $parameters
     */
    public function trans(string $key, array $parameters = []): string
    {
        $message = $this->getMessages()[$key] ?? $key;

        $replacements = [];

        foreach ($parameters as $name => $value) {
            $replacements['%' . $name . '%'] = (string) $value;
        }

        return strtr($message, $replacements);
    }

    /**
     * @return array<string, string>
     */
    private function getMessages(): array
    {
        if ($this->messages !== null) {
            return $this->messages;
        }

        $file = $this->translationsPath . '/' . $this->locale . '.php';

        if (is_file($file) === false) {
            throw new RuntimeException(sprintf('Translation file "%s" not found.', $file));
        }

        /** @var array<string, string> $messages */
        $messages = require $file;

        return $this->messages = $messages;
    }
}
