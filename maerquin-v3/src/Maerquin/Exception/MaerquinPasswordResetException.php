<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception;

use RuntimeException;

final class MaerquinPasswordResetException extends RuntimeException
{
    public static function tooShort(): self
    {
        return new self('Password is too short. Minimum 8 characters required.');
    }

    public static function notMatching(): self
    {
        return new self('Passwords do not match.');
    }
}
