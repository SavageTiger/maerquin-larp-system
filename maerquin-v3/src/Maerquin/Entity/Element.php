<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Element as ElementModel;

#[ORM\Entity]
class Element extends ElementModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    protected UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $name;
}
