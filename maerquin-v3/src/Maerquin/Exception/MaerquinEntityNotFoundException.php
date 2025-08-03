<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception;

use Exception;

class MaerquinEntityNotFoundException extends Exception
{
    public static function withType(string $entityName): self
    {
        return new self('Could not find entity of type ' . $entityName);
    }
}
