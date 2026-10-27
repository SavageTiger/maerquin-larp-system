<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Entity\FormField as FormFieldEntity;

class FormField
{
    protected UuidInterface $id;
    protected Form $form;
    protected string $name;
    protected FormFieldType $type;
    protected string $label;
    protected bool $required;
    protected int $row;
    protected int $column;

    public static function create(
        Form $form,
        string $name,
        FormFieldType $type,
        string $label,
        bool $required,
        int $row,
        int $column,
    ): FormFieldEntity {
        $field = new FormFieldEntity();
        $field->id = Uuid::uuid4();
        $field->form = $form;
        $field->name = $name;
        $field->type = $type;
        $field->label = $label;
        $field->required = $required;
        $field->row = $row;
        $field->column = $column;

        return $field;
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
