<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception\Form;

use RuntimeException;

final class FormDeleteException extends RuntimeException
{
    private function __construct(
        string $message,
        private readonly string $translationKey,
    ) {
        parent::__construct($message);
    }

    public static function linkedToEvents(): self
    {
        return new self('Form is linked to one or more events.', 'forms.delete.error.linked_events');
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
