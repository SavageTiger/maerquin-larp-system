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
    protected string $key;
    protected FormFieldType $type;
    protected string $label;
    protected bool $required;
    protected int $row;
    protected int $column;
    protected bool $deleted;

    public static function create(
        FormSection $section,
        UuidInterface $fieldId,
        FormFieldType $type,
        string $label,
        bool $required,
        int $row,
        int $column,
    ): FormFieldEntity {
        $field = new FormFieldEntity();
        $field->id = $fieldId;
        $field->section = $section;
        $field->key = Uuid::uuid4()->toString();
        $field->type = $type;
        $field->label = $label;
        $field->required = $required;
        $field->row = $row;
        $field->column = $column;
        $field->deleted = false;

        return $field;
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function getKey(): string
    {
        return $this->key;
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

    public function update(string $label, FormFieldType $type, bool $required, int $row, int $column): void
    {
        $this->label = $label;
        $this->type = $type;
        $this->required = $required;
        $this->row = $row;
        $this->column = $column;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function delete(): void
    {
        $this->deleted = true;
    }
}
