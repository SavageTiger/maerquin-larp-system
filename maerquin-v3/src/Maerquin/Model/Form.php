<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\Form as FormEntity;
use SvenHK\Maerquin\Entity\FormField;

class Form
{
    protected UuidInterface $id;

    protected string $name;

    /**
     * @var Collection<int, FormField>
     */
    protected Collection $fields;

    public static function create(string $name): FormEntity
    {
        $form = new FormEntity();
        $form->id = Uuid::uuid4();
        $form->name = $name;
        $form->fields = new ArrayCollection();

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

    /**
     * @return list<FormField>
     */
    public function getFields(): array
    {
        return array_values($this->fields->toArray());
    }
}
