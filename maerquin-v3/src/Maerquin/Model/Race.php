<?php

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;

class Race
{
    protected UuidInterface $id;
    protected string $name;

    public function serialize()
    {

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }
}

