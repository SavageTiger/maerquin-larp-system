<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Exception\Subscription;

use RuntimeException;

final class SubscriptionException extends RuntimeException
{
    private function __construct(
        string $message,
        private readonly string $translationKey,
    ) {
        parent::__construct($message);
    }

    public static function notOpen(): self
    {
        return new self('Subscriptions for this event are not open.', 'subscribe.error.not_open');
    }

    public static function formMissing(): self
    {
        return new self('This event has no subscription form yet.', 'subscribe.error.form_missing');
    }

    public static function alreadySubscribed(): self
    {
        return new self('The player is already subscribed to this event.', 'subscribe.error.already_subscribed');
    }

    public static function notFound(): self
    {
        return new self('The subscription does not belong to this event.', 'subscribe.error.not_found');
    }

    public static function roleInvalid(string $role): self
    {
        return new self(sprintf('"%s" is not a valid role.', $role), 'subscribe.error.role_invalid');
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
