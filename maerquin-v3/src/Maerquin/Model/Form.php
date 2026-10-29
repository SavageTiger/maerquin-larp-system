<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Form as FormEntity;
use SvenHK\Maerquin\Entity\FormSection as FormSectionEntity;

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

    public function addSection(string $title, null | int $position = null): FormSectionEntity
    {
        assert($this instanceof FormEntity);

        $sections = $this->getSections();

        $index = $position === null
            ? count($sections)
            : max(0, min($position - 1, count($sections)));

        $section = FormSection::create($this, $title, $index + 1);

        array_splice($sections, $index, 0, [$section]);

        $this->sections->add($section);
        $this->recalculateSectionPositions($sections);

        return $section;
    }

    public function getSections(): array
    {
        $sections = array_values($this->sections->toArray());

        usort(
            $sections,
            static fn(FormSectionEntity $a, FormSectionEntity $b): int => $a->getPosition() <=> $b->getPosition(),
        );

        return $sections;
    }

    /**
     * @param list<FormSectionEntity> $sections in the desired order
     */
    private function recalculateSectionPositions(array $sections): void
    {
        foreach ($sections as $index => $section) {
            $section->reposition($index + 1);
        }
    }
}
