<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\FormField as FormFieldModel;
use SvenHK\Maerquin\Model\FormFieldType;

#[ORM\Entity]
#[ORM\Table(name: 'form_field')]
#[ORM\UniqueConstraint(name: 'unique_form_field_name', columns: ['form_id', 'name'])]
class FormField extends FormFieldModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(name: 'form_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected Form $form;

    #[ORM\Column(length: 128)]
    protected string $name;

    #[ORM\Column(length: 16, enumType: FormFieldType::class)]
    protected FormFieldType $type;

    #[ORM\Column(length: 255)]
    protected string $label;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    protected bool $required;

    #[ORM\Column(name: 'form_row', type: 'integer')]
    protected int $row;

    #[ORM\Column(name: 'form_column', type: 'integer')]
    protected int $column;
}
