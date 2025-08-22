<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use Override;
use App\Domain\Entity\User as FrameworkUser;

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
