<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use App\Domain\Entity\User as FrameworkUser;
use Override;

class Anonymous extends FrameworkUser
{
    #[Override]
    public function getId(): string
    {
        return 'anonymous';
    }

    #[Override]
    public function isAdmin(): bool
    {
        return false;
    }
}
