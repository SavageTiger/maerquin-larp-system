<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;

enum FormFieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Email = 'email';
    case Number = 'number';
    case Date = 'date';
    case Checkbox = 'checkbox';

    public function accepts(string $value): bool
    {
        return match ($this) {
            self::Email => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            self::Number => is_numeric($value),
            self::Date => DateTimeImmutable::createFromFormat('!Y-m-d', $value)?->format('Y-m-d') === $value,
            self::Checkbox => $value === '1',
            self::Text, self::Textarea => true,
        };
    }
}
