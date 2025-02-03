<?php

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;

class Character
{
    protected UuidInterface $id;
    protected string $name;

    public function serialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'title' => $this->getTitle(),
            'playerId' => $this->playerId()
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function playerId(): string
    {
        return $this->player->getId();
    }
}
