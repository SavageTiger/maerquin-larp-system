<?php

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;

class Deity
{
    protected UuidInterface $id;
    protected string $name;

    public function serialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }
}

