<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

class DeitiesCollection
{
    public function __construct(
        /**
         * @var Deity[]
         */
        private readonly array $deities
    )
    {
    }

    public function serialize(): array
    {
        return array_map(
            fn(Deity $deity): array => $deity->serialize(),
            $this->deities,
        );
    }
}
