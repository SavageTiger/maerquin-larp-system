<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

use DateTimeImmutable;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Token
{
    public const string TYPE_REMEMBER_ME = 'REMEMBER_ME';
    public const string TYPE_RESET_PASSWORD = 'RESET_PASSWORD';

    protected UuidInterface $id;
    protected User $user;
    protected string $type;
    protected string $value;
    protected null | string $unhashedValue = null;
    protected DateTimeImmutable $createdAt;

    public static function generateForUser(
        User $user,
    ): self {
        $token = new static();

        $tokenValue = self::generateToken();

        $token->id = Uuid::uuid4();
        $token->user = $user;
        $token->type = self::TYPE_REMEMBER_ME;
        $token->value = hash('sha256', $tokenValue);
        $token->createdAt = new DateTimeImmutable();
        $token->unhashedValue = $tokenValue;

        return $token;
    }

    private static function generateToken(): string
    {
        return sprintf(
            '%s:%s',
            time(),
            hash('sha256', random_bytes(1_024 * 2)),
        );
    }

    public static function generateForPasswordReset(
        User $user,
    ): self {
        $token = new static();

        $token->id = Uuid::uuid4();
        $token->user = $user;
        $token->type = self::TYPE_RESET_PASSWORD;
        $token->value = hash('sha256', self::generateToken());
        $token->createdAt = new DateTimeImmutable();
        $token->unhashedValue = $token->value;

        return $token;
    }

    public function getUnhashedValue(): string
    {
        if ($this->unhashedValue === null) {
            throw new Exception('Unhashed value not available at this stage');
        }

        return $this->unhashedValue;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isValid(): bool
    {
        $expiration = (clone $this->createdAt)->modify('+30 days');

        return new DateTimeImmutable() < $expiration;
    }
}
