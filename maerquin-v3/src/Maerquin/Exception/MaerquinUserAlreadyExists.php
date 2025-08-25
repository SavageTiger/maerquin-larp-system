<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception;

use Exception;

class MaerquinUserAlreadyExists extends Exception
{
    public static function withUsername(string $username): self
    {
        return new self(
            sprintf('A user with the username "%s" already exists.', $username),
        );
    }
}
