<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider\Platform;

use SvenHK\Maerquin\Exception\Status\SystemInfoNotAvailableException;
use SvenHK\Maerquin\Status\Results\LoadAverage;

/**
 * sys_getloadavg() works on both Linux and FreeBSD.
 */
class LoadAverageReader
{
    /**
     * @throws SystemInfoNotAvailableException
     */
    public function read(): LoadAverage
    {
        $load = function_exists('sys_getloadavg') ? sys_getloadavg() : false;

        if ($load === false || count($load) < 3) {
            throw SystemInfoNotAvailableException::loadAverageUnavailable();
        }

        return new LoadAverage((float)$load[0], (float)$load[1], (float)$load[2]);
    }
}
