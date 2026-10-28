<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Translation;

use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Exposes the Translator to templates: {{ 'some.key'|trans({'name': value}) }}
 */
final class TranslationExtension extends AbstractExtension
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
            new TwigFilter('trans', $this->translator->trans(...)),
        ];
    }
}
