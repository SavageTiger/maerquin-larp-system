<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Provider;

use SvenHK\Maerquin\Status\Results\ServerStatus;

class ServerStatusProvider
{
    public function getStatus(): ServerStatus
    {
        return new ServerStatus(
            os: php_uname('s'),
            hostname: php_uname('n'),
            kernel: php_uname('r'),
            kernelBuild: php_uname('v'),
            architecture: php_uname('m'),
        );
    }
}
