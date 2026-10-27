<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

enum FormFieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Email = 'email';
    case Number = 'number';
    case Date = 'date';
    case Checkbox = 'checkbox';
}
