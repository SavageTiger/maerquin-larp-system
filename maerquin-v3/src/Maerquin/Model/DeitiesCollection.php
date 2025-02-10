<?php

namespace SvenHK\Maerquin\Model;

class DeitiesCollection
{
    /**
     * @var Deity[]
     */
    private array $deities;

    public function __construct(array $deities)
    {
        $this->deities = $deities;
    }

    public function serialize() : array
    {
        return array_map(
            fn(Deity $deity) => $deity->serialize(),
            $this->deities
        );
    }
}

