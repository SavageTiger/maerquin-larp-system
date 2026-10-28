<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

final readonly class LoadAverage
{
    public function __construct(
        public float $oneMinute,
        public float $fiveMinutes,
        public float $fifteenMinutes,
    ) {
    }
}
