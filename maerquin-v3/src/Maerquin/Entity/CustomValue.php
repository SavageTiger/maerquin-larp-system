<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\CustomValue as CustomValueModel;

#[ORM\Entity]
class CustomValue extends CustomValueModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: CustomField::class)]
    #[ORM\JoinColumn(name: 'customfield_id', referencedColumnName: 'id')]
    protected CustomField $customField;

    #[ORM\Column(name: 'entity_id', type: 'uuid')]
    protected string $entityId;

    #[ORM\Column(type: 'text', nullable: true)]
    protected string $value;
}
