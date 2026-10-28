<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Results;

/**
 * A part of the status page that can be unavailable (e.g. a service that is down).
 */
interface StatusSection
{
    public function isAvailable(): bool;
}
