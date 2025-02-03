<?php

namespace SvenHK\Maerquin\Model;

class DietiesCollection
{
    /**
     * @var Deity[]
     */
    private array $dieties;

    public function __construct(array $dieties)
    {
        $this->dieties = $dieties;
    }

    public function serialize(): array
    {
        return array_map(
            fn(Deity $diety) => $diety->serialize(),
            $this->dieties
        );
    }
}

