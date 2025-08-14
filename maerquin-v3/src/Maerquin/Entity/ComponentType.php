<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
class ComponentType
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    public UuidInterface $id;

    #[ORM\Column(length: 255)]
    public string $name;
}
