<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception\Form;

use RuntimeException;

final class FormSectionException extends RuntimeException
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

    public static function sectionNotInForm(): self
    {
        return new self('The section does not belong to this form.', 'forms.section.error.section_not_in_form');
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
