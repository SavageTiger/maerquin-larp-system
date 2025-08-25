<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use App\Domain\Entity\User as FrameworkUser;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class User extends FrameworkUser
{
    protected UuidInterface $id;
    protected string $username;
    protected DateTimeImmutable $lastLogin;
    protected bool $admin;

    public function getId(): string
    {
        return (string)$this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function checkPassword(string $password): bool
    {
        $algorithm = 'sha512';
        $iterations = 1_000;

        $derivedHash = hash_pbkdf2(
            $algorithm,
            $password,
            base64_decode($this->salt),
            $iterations,
            strlen(base64_decode($this->hash)),
            true,
        );

        return hash_equals(base64_decode($this->hash), $derivedHash);
    }

    public function getName(): string
    {
        return $this->username;
    }

    public function getLastLogin(): DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function loggedIn(): void
    {
        $this->lastLogin = new DateTimeImmutable();
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function promote(): void
    {
        $this->admin = true;
    }

    public function demote(): void
    {
        $this->admin = false;
    }

    public function isAnonymous(): bool
    {
        return false;
    }
}
