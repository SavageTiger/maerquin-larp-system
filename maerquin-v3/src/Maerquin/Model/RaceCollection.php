<?php

namespace SvenHK\Maerquin\Model;

class RaceCollection
{
    /**
     * @var Race[]
     */
    private array $races;

    public function __construct(array $races)
    {
        $this->races = $races;
    }

    public function serialize() : array
    {
        foreach ($this->races as $race) {
            $serialized[] = $race->serialize();
        }

        return $serialized;
    }
}

