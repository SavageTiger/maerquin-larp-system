<?php

namespace SvenHK\Maerquin\Model;

use App\Domain\Entity\User as FrameworkUser;

class Anonymous extends FrameworkUser
{
    public function getId(): string
    {
        return 'anonymous';
    }

    public function isAdmin(): bool
    {
        return false;
    }
}

