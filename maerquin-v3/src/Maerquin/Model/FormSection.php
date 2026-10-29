<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Form;
use SvenHK\Maerquin\Entity\FormField;
use SvenHK\Maerquin\Entity\FormSection as FormSectionEntity;

class FormSection
{
    protected UuidInterface $id;

    protected Form $form;

    protected string $title;

    protected null | string $description;

    protected int $position;

    /**
     * @var Collection<int, FormField>
     */
    protected Collection $fields;

    public static function create(
        Form $form,
        string $title,
        int $position,
        null | string $description = null,
    ): FormSectionEntity {
        $section = new FormSectionEntity();
        $section->id = Uuid::uuid4();
        $section->form = $form;
        $section->title = $title;
        $section->description = $description;
        $section->position = $position;
        $section->fields = new ArrayCollection();

        return $section;
    }

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function reposition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return list<FormField>
     */
    public function getFields(): array
    {
        return array_values($this->fields->toArray());
    }
}
