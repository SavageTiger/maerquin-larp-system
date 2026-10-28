<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Status\Twig;

use Override;
use SvenHK\Maerquin\Translation\Translator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Formatting filters for the raw values in the status results.
 */
class StatusExtension extends AbstractExtension
{
    private const array BYTE_UNITS = ['B', 'KB', 'MB', 'GB', 'TB'];

    public function __construct(
        private readonly Translator $translator,
    ) {
    }

    /**
     * @return TwigFilter[]
     */
    #[Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('bytes', [$this, 'formatBytes']),
            new TwigFilter('duration', [$this, 'formatDuration']),
        ];
    }

    public function formatBytes(int $bytes): string
    {
        $size = (float)$bytes;
        $unit = 0;

        while ($size >= 1_000 && $unit < count(self::BYTE_UNITS) - 1) {
            $size /= 1_024;
            $unit++;
        }

        return number_format($size, $unit === 0 ? 0 : 2, ',', '.') . ' ' . self::BYTE_UNITS[$unit];
    }

    public function formatDuration(int $seconds): string
    {
        $days = intdiv($seconds, 86_400);
        $hours = intdiv($seconds % 86_400, 3_600);
        $minutes = intdiv($seconds % 3_600, 60);

        $parameters = [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds % 60,
        ];

        if ($days > 0) {
            return $this->translator->trans('status.duration.days', $parameters);
        }

        if ($hours > 0) {
            return $this->translator->trans('status.duration.hours', $parameters);
        }

        return $this->translator->trans('status.duration.minutes', $parameters);
    }
}
