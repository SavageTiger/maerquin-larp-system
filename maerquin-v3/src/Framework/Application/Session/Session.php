<?php

declare(strict_types=1);

namespace App\Application\Session;

use App\Domain\Entity\User;

interface Session
{
    public function setUser(User $user): void;

    public function getUser(): User;
}
