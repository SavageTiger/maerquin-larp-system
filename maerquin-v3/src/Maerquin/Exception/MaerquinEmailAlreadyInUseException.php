<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception;

use RuntimeException;

final class MaerquinEmailAlreadyInUseException extends RuntimeException
{
    public static function withEmail(string $email): self
    {
        return new self(sprintf('The email address "%s" is already in use.', $email));
    }
}
