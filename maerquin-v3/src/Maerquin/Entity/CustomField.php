<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\CustomField as CustomFieldModel;
use SvenHK\Maerquin\Repository\CustomFieldRepository;

#[ORM\Entity(repositoryClass: CustomFieldRepository::class)]
class CustomField extends CustomFieldModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    protected UuidInterface $id;

    #[ORM\Column(length: 255)]
    protected string $tableName;

    #[ORM\Column(length: 255)]
    protected string $name;

    #[ORM\Column(type: 'integer')]
    protected int $ordinal;
}
