<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Entity\FormField as FormFieldEntity;
use SvenHK\Maerquin\Entity\FormSection as FormSectionEntity;
use SvenHK\Maerquin\Exception\Form\FormFieldException;

class FormSection
{
    public const int MAX_FIELDS_PER_ROW = 3;

    protected UuidInterface $id;

    protected Form $form;

    protected string $title;

    protected int $position;

    protected bool $deleted;

    /**
     * @var Collection<int, FormFieldEntity>
     */
    protected Collection $fields;

    public static function create(
        Form $form,
        string $title,
        int $position,
    ): FormSectionEntity {
        $section = new FormSectionEntity();
        $section->id = Uuid::uuid4();
        $section->form = $form;
        $section->title = $title;
        $section->position = $position;
        $section->deleted = false;
        $section->fields = new ArrayCollection();

        return $section;
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function delete(): void
    {
        $this->deleted = true;
    }

    public function reposition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return list<FormFieldEntity>
     */
    public function getFields(): array
    {
        return array_values(array_filter(
            $this->fields->toArray(),
            static fn(FormFieldEntity $field): bool => $field->isDeleted() === false,
        ));
    }

    /**
     * @param list<list<array{id: UuidInterface, label: string, type: FormFieldType, required: bool}>> $rows
     *
     * @throws FormFieldException
     */
    public function replaceFields(array $rows): void
    {
        assert($this instanceof FormSectionEntity);

        $remainingFields = [];

        foreach ($this->getFields() as $field) {
            $remainingFields[$field->getId()] = $field;
        }

        foreach ($rows as $rowIndex => $fields) {
            if (count($fields) > self::MAX_FIELDS_PER_ROW) {
                throw FormFieldException::rowFull(self::MAX_FIELDS_PER_ROW);
            }

            foreach ($fields as $columnIndex => $data) {
                if ($data['label'] === '') {
                    throw FormFieldException::labelMissing();
                }

                $fieldId = $data['id']->toString();
                $row = $rowIndex + 1;
                $column = $columnIndex + 1;

                if (isset($remainingFields[$fieldId])) {
                    $remainingFields[$fieldId]->update($data['label'], $data['type'], $data['required'], $row, $column);

                    unset($remainingFields[$fieldId]);

                    continue;
                }

                $this->fields->add(
                    FormField::create($this, $data['id'], $data['type'], $data['label'], $data['required'], $row, $column),
                );
            }
        }

        foreach ($remainingFields as $removedField) {
            $removedField->delete();
        }
    }
}
