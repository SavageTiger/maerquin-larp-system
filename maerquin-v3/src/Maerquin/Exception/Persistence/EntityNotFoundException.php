<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception\Persistence;

use Exception;

class EntityNotFoundException extends Exception
{
    public static function withType(string $entityName): self
    {
        return new self('Could not find entity of type ' . $entityName);
    }
}
