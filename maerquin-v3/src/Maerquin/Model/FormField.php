<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\FormField as FormFieldEntity;
use SvenHK\Maerquin\Entity\FormSection;

class FormField
{
    protected UuidInterface $id;
    protected FormSection $section;
    protected string $name;
    protected FormFieldType $type;
    protected string $label;
    protected bool $required;
    protected int $row;
    protected int $column;

    public static function create(
        FormSection $section,
        string $name,
        FormFieldType $type,
        string $label,
        bool $required,
        int $row,
        int $column,
    ): FormFieldEntity {
        $field = new FormFieldEntity();
        $field->id = Uuid::uuid4();
        $field->section = $section;
        $field->name = $name;
        $field->type = $type;
        $field->label = $label;
        $field->required = $required;
        $field->row = $row;
        $field->column = $column;

        return $field;
    }

    public function getSection(): FormSection
    {
        return $this->section;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): FormFieldType
    {
        return $this->type;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function getColumn(): int
    {
        return $this->column;
    }
}
