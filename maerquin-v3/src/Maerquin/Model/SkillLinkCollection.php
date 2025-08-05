<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

final readonly class SkillLinkCollection
{
    /**
     * @var array<int, SkillLink>
     */
    private array $skillLinks;

    /**
     * @param array $skillLinks <int, SkillLink>
     */
    public function __construct(array $skillLinks)
    {
        $this->skillLinks = $skillLinks;
    }

    public function getSkillLinks(): array
    {
        return $this->skillLinks;
    }
}
