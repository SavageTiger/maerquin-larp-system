<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception\Account;

use RuntimeException;

final class EmailAlreadyInUseException extends RuntimeException
{
    public static function withEmail(string $email): self
    {
        return new self(sprintf('The email address "%s" is already in use.', $email));
    }
}
