<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception\Form;

use RuntimeException;

final class FormFieldException extends RuntimeException
{
    private function __construct(
        string $message,
        private readonly string $translationKey,
    ) {
        parent::__construct($message);
    }

    public static function labelMissing(): self
    {
        return new self('A field needs a label.', 'forms.field.error.label_missing');
    }

    public static function typeInvalid(string $type): self
    {
        return new self(sprintf('"%s" is not a valid field type.', $type), 'forms.field.error.type_invalid');
    }

    public static function layoutInvalid(): self
    {
        return new self('The layout of the fields is not valid.', 'forms.field.error.layout_invalid');
    }

    public static function rowFull(int $maximum): self
    {
        return new self(sprintf('A row holds at most %d fields.', $maximum), 'forms.field.error.row_full');
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
