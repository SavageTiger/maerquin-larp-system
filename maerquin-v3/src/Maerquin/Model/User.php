<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use App\Domain\Entity\User as FrameworkUser;
use Ramsey\Uuid\UuidInterface;

class User extends FrameworkUser
{
    protected UuidInterface $id;
    protected string $username;

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
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
