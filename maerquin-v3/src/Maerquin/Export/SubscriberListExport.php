<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Export;

use RuntimeException;
use SvenHK\Maerquin\Entity\Event;
use SvenHK\Maerquin\Model\SubscriptionRole;
use SvenHK\Maerquin\Translation\Translator;

final readonly class SubscriberListExport
{
    private const string SEPARATOR = ';';
    private const string UTF8_BOM = "\xEF\xBB\xBF";

    public function __construct(
        private Translator $translator,
    ) {
    }

    public function toCsv(Event $event): string
    {
        $handle = fopen('php://temp', 'r+') ?: throw new RuntimeException('Could not open a temporary stream');

        $this->writeRow($handle, [
            $this->translator->trans('admin.subscriptions.export.name'),
            $this->translator->trans('admin.subscriptions.export.email'),
            $this->translator->trans('admin.subscriptions.export.role'),
        ]);

        foreach (SubscriptionRole::cases() as $role) {
            foreach ($event->getSubscriptions($role) as $subscription) {
                $player = $subscription->getPlayer();

                $this->writeRow($handle, [
                    $player->getName(),
                    $player->getEmail(),
                    $this->translator->trans('subscribe.role.' . $role->value),
                ]);
            }
        }

        rewind($handle);
        $csv = (string)stream_get_contents($handle);
        fclose($handle);

        return self::UTF8_BOM . $csv;
    }

    /**
     * @param resource $handle
     * @param list<string> $values
     */
    private function writeRow($handle, array $values): void
    {
        fputcsv($handle, array_map($this->neutralizeFormula(...), $values), self::SEPARATOR, '"', '');
    }

    private function neutralizeFormula(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) === 1 ? "'" . $value : $value;
    }
}
