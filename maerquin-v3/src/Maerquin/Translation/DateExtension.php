<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Translation;

use DateTimeImmutable;
use DateTimeInterface;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class DateExtension extends AbstractExtension
{
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
            new TwigFilter('long_date', $this->longDate(...)),
            new TwigFilter('date_range', $this->dateRange(...)),
            new TwigFilter('days_until', $this->daysUntil(...)),
        ];
    }

    public function longDate(DateTimeInterface $date, bool $withYear = true): string
    {
        $longDate = sprintf(
            '%d %s',
            (int)$date->format('j'),
            $this->translator->trans('date.month.' . $date->format('n')),
        );

        return $withYear ? $longDate . ' ' . $date->format('Y') : $longDate;
    }

    public function dateRange(DateTimeInterface $start, DateTimeInterface $end): string
    {
        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
            return $this->longDate($start);
        }

        $from = match (true) {
            $start->format('Y-m') === $end->format('Y-m') => $start->format('j'),
            $start->format('Y') === $end->format('Y') => $this->longDate($start, false),
            default => $this->longDate($start),
        };

        return $this->translator->trans('date.range', ['from' => $from, 'to' => $this->longDate($end)]);
    }

    public function daysUntil(DateTimeInterface $date): string
    {
        $days = (int)new DateTimeImmutable('today')->diff(DateTimeImmutable::createFromInterface($date))->format('%r%a');

        return match ($days) {
            0 => $this->translator->trans('date.today'),
            1 => $this->translator->trans('date.tomorrow'),
            default => $this->translator->trans('date.days_until', ['days' => $days]),
        };
    }
}
