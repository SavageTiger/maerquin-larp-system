<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;

class SkillType
{
    protected UuidInterface $id;
    protected string $name;
    protected int $ordinal;

    public function serialize()
    {
        return ['id' => $this->getId(), 'name' => $this->getName(), 'ordering' => $this->ordinal];
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrdering(): string
    {
        return $this->ordinal;
    }
}
