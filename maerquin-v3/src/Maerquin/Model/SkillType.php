<?php

namespace SvenHK\Maerquin\Model;

use Ramsey\Uuid\UuidInterface;

class SkillType
{
    protected UuidInterface $id;
    protected string $name;
    
    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }
}

