<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Form as FormEntity;
use SvenHK\Maerquin\Entity\FormSection as FormSectionEntity;
use SvenHK\Maerquin\Exception\Form\FormFieldException;
use SvenHK\Maerquin\Exception\Form\FormSectionException;

class Form
{
    protected UuidInterface $id;

    protected string $name;

    /**
     * @var Collection<int, FormSectionEntity>
     */
    protected Collection $sections;

    public static function create(UuidInterface $formId, string $name = ''): FormEntity
    {
        $form = new FormEntity();
        $form->id = $formId;
        $form->name = $name;
        $form->sections = new ArrayCollection();

        return $form;
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @throws FormSectionException
     */
    public function addSection(string $title): FormSectionEntity
    {
        assert($this instanceof FormEntity);

        $title = trim($title);

        if ($title === '') {
            throw FormSectionException::titleMissing();
        }

        $section = FormSection::create($this, $title, count($this->getSections()) + 1);

        $this->sections->add($section);

        return $section;
    }

    public function getSections(): array
    {
        $sections = array_values(array_filter(
            $this->sections->toArray(),
            static fn(FormSectionEntity $section): bool => $section->isDeleted() === false,
        ));

        usort(
            $sections,
            static fn(FormSectionEntity $a, FormSectionEntity $b): int => $a->getPosition() <=> $b->getPosition(),
        );

        return $sections;
    }

    /**
     * @param list<list<array{id: UuidInterface, label: string, type: FormFieldType, required: bool}>> $rows
     *
     * @throws FormFieldException
     */
    public function replaceSectionFields(FormSectionEntity $section, array $rows): void
    {
        if ($this->sections->contains($section) === false) {
            throw FormFieldException::sectionNotInForm();
        }

        $fieldIdsInOtherSections = [];

        foreach ($this->sections as $otherSection) {
            if ($otherSection === $section) {
                continue;
            }

            foreach ($otherSection->getFields() as $field) {
                $fieldIdsInOtherSections[$field->getId()] = true;
            }
        }

        $seenFieldIds = [];

        foreach ($rows as $rowIndex => $fields) {
            if ($fields === []) {
                throw FormFieldException::layoutInvalid();
            }

            if (count($fields) > FormSection::MAX_FIELDS_PER_ROW) {
                throw FormFieldException::rowFull(FormSection::MAX_FIELDS_PER_ROW);
            }

            foreach ($fields as $columnIndex => $field) {
                $fieldId = $field['id']->toString();

                if (isset($seenFieldIds[$fieldId]) || isset($fieldIdsInOtherSections[$fieldId])) {
                    throw FormFieldException::layoutInvalid();
                }

                $seenFieldIds[$fieldId] = true;

                $label = trim($field['label']);

                if ($label === '') {
                    throw FormFieldException::labelMissing();
                }

                $rows[$rowIndex][$columnIndex]['label'] = $label;
            }
        }

        $section->replaceFields($rows);
    }

    /**
     * @throws FormSectionException
     */
    public function deleteSection(FormSectionEntity $section): void
    {
        if (in_array($section, $this->getSections(), true) === false) {
            throw FormSectionException::sectionNotInForm();
        }

        $section->delete();

        $this->recalculateSectionPositions($this->getSections());
    }

    public function findSection(string $sectionId): null | FormSectionEntity
    {
        foreach ($this->getSections() as $section) {
            if ($section->getId() === $sectionId) {
                return $section;
            }
        }

        return null;
    }

    /**
     * @param list<FormSectionEntity> $sections
     */
    private function recalculateSectionPositions(array $sections): void
    {
        foreach ($sections as $index => $section) {
            $section->reposition($index + 1);
        }
    }
}
