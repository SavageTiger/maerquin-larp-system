<?php

namespace SvenHK\Maerquin\Session;

class Session
{
    public function write(string $key, string|int|bool $value): void
    {
        $_SESSION[$key] = $value;
    }


    public function read(string $key, string|int|bool $defaultValue): string|int|bool
    {
        return $_SESSION[$key] ?? $defaultValue;
    }
}
