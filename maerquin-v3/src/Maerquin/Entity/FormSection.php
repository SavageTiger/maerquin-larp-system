<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\FormSection as FormSectionModel;

#[ORM\Entity]
#[ORM\Table(name: 'form_section')]
class FormSection extends FormSectionModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'sections')]
    #[ORM\JoinColumn(name: 'form_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected Form $form;

    #[ORM\Column(length: 255)]
    protected string $title;

    #[ORM\Column(type: 'integer')]
    protected int $position;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    protected bool $deleted;

    #[ORM\OneToMany(
        targetEntity: FormField::class,
        mappedBy: 'section',
        cascade: ['persist'],
    )]
    #[ORM\OrderBy(['row' => 'ASC', 'column' => 'ASC'])]
    protected Collection $fields;
}
