<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use App\Domain\Entity\User as FrameworkUser;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Entity\User as UserEntity;
use SvenHK\Maerquin\Model\Player as PlayerModel;

class User extends FrameworkUser
{
    protected UuidInterface $id;
    protected string $username;
    protected DateTimeImmutable $lastLogin;
    protected PlayerModel $player;
    protected bool $admin;
    protected string $salt;
    protected string $hash;

    public static function create(PlayerModel $player): UserEntity
    {
        $user = new UserEntity();

        $user->id = Uuid::uuid4();
        $user->username = '';
        $user->player = $player;
        $user->lastLogin = new DateTimeImmutable('1970-1-1');
        $user->admin = false;
        $user->updatePassword(random_bytes(36));

        return $user;
    }

    public function updatePassword(string $password): void
    {
        $algorithm = 'sha512';
        $iterations = 1_000;

        $this->salt = base64_encode(random_bytes(64));
        $this->hash = base64_encode(hash_pbkdf2(
            $algorithm,
            $password,
            base64_decode($this->salt),
            $iterations,
            strlen($password),
            true,
        ));
    }

    public function changeUsername(string $username): void
    {
        $this->username = $username;
    }

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
