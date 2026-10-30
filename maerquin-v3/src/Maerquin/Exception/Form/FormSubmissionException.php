<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception\Form;

use RuntimeException;

final class FormSubmissionException extends RuntimeException
{
    /**
     * @param array<string, string> $translationParameters
     */
    private function __construct(
        string $message,
        private readonly string $translationKey,
        private readonly array $translationParameters,
    ) {
        parent::__construct($message);
    }

    public static function answerMissing(string $label): self
    {
        return new self(sprintf('"%s" is required.', $label), 'subscribe.error.answer_missing', ['field' => $label]);
    }

    public static function answerInvalid(string $label): self
    {
        return new self(sprintf('"%s" is not valid.', $label), 'subscribe.error.answer_invalid', ['field' => $label]);
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    /**
     * @return array<string, string>
     */
    public function getTranslationParameters(): array
    {
        return $this->translationParameters;
    }
}
