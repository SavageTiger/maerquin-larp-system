<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception\Account;

use RuntimeException;

final class RegistrationException extends RuntimeException
{
    private function __construct(
        string $message,
        private readonly string $translationKey,
    ) {
        parent::__construct($message);
    }

    public static function firstNameMissing(): self
    {
        return new self('First name is missing.', 'registration.error.first_name_missing');
    }

    public static function lastNameMissing(): self
    {
        return new self('Last name is missing.', 'registration.error.last_name_missing');
    }

    public static function invalidEmail(): self
    {
        return new self('Email address is invalid.', 'registration.error.invalid_email');
    }

    public static function passwordTooShort(): self
    {
        return new self(
            'Password is too short. Minimum 8 characters required.',
            'registration.error.password_too_short',
        );
    }

    public static function passwordsNotMatching(): self
    {
        return new self('Passwords do not match.', 'registration.error.passwords_not_matching');
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
