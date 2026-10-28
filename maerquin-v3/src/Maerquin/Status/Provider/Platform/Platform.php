<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

use SvenHK\Maerquin\Status\Results\CpuStatus;
use SvenHK\Maerquin\Status\Results\MemoryStatus;
use SvenHK\Maerquin\Status\Results\Unavailable;

/**
 * Operating system specific way of reading hardware information.
 */
interface Platform
{
    public function getCpuStatus(): CpuStatus|Unavailable;

    public function getMemoryStatus(): MemoryStatus|Unavailable;
}
