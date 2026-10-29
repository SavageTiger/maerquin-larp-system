<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception;

use RuntimeException;

final class MaerquinFormSectionException extends RuntimeException
{
    private function __construct(
        string $message,
        private readonly string $translationKey,
    ) {
        parent::__construct($message);
    }

    public static function titleMissing(): self
    {
        return new self('A section needs a title.', 'forms.section.error.title_missing');
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
