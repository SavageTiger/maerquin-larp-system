<?php

namespace SvenHK\Maerquin\Model;

use App\Domain\Entity\User as FrameworkUser;

class User extends FrameworkUser
{
    public function getId(): string
    {
        return $this->id;
    }

    public function isAdmin(): bool
    {
        return true;
    }

    public function isAnonymous(): bool
    {
        return false;
    }
}

