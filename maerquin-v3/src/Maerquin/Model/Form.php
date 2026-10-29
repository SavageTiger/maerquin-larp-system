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

    /**
     * @return list<FormSectionEntity>
     */
    public function getSections(): array
    {
        return array_values(array_filter(
            $this->sections->toArray(),
            static fn(FormSectionEntity $section): bool => $section->isDeleted() === false,
        ));
    }

    /**
     * @param list<list<array{id: UuidInterface, label: string, type: FormFieldType, required: bool}>> $rows
     *
     * @throws FormFieldException
     */
    public function replaceSectionFields(FormSectionEntity $section, array $rows): void
    {
        $section->replaceFields($rows);
    }

    public function deleteSection(FormSectionEntity $section): void
    {
        $section->delete();

        foreach ($this->getSections() as $index => $remainingSection) {
            $remainingSection->reposition($index + 1);
        }
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
}
