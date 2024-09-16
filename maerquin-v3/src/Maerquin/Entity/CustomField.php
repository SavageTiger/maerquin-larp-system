<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class CustomField
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public Uuid $id;

    #[ORM\Column(length: 255)]
    public string $tableName;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column(type: 'integer')]
    public int $ordinal;
}
