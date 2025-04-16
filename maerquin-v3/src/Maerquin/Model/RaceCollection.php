<?php

declare(strict_types=1);

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
        return array_map(
            fn(Race $race) => $race->serialize(),
            $this->races,
        );
    }
}
