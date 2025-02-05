<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\SkillType as SkillTypeModel;
use SvenHK\Maerquin\Repository\SkillTypeRepository;

#[ORM\Entity(repositoryClass: SkillTypeRepository::class)]
#[ORM\Table(name: "skilltype")]
class SkillType extends SkillTypeModel
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name;

    #[ORM\Column(type: 'integer')]
    protected int $ordinal;
}
