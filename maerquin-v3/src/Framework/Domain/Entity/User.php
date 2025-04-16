<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class User
{
    public function getId() : string
    {
        return '';
    }

    public function isAdmin() : bool
    {
        return false;
    }

    public function isAnonymous() : bool
    {
        return true;
    }
}
