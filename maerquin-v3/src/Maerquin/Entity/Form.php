<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Form as FormModel;

#[ORM\Entity]
#[ORM\Table(name: 'form')]
class Form extends FormModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\Column(length: 255)]
    protected string $name;

    #[ORM\OneToMany(
        targetEntity: FormSection::class,
        mappedBy: 'form',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    protected Collection $sections;
}
