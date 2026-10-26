<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception;

use RuntimeException;

final class MaerquinRegistrationException extends RuntimeException
{
    public static function nameMissing(): self
    {
        return new self('Vul je naam in.');
    }

    public static function invalidEmail(): self
    {
        return new self('Vul een geldig e-mailadres in.');
    }

    public static function passwordTooShort(): self
    {
        return new self('Het wachtwoord moet minimaal 8 tekens lang zijn.');
    }

    public static function passwordsNotMatching(): self
    {
        return new self('De wachtwoorden komen niet overeen.');
    }
}
