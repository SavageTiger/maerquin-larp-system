<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\CharacterEventLink as CharacterEventLinkModel;

#[ORM\Entity]
#[ORM\Table(name: 'characterEventLink')]
class CharacterEventLink extends CharacterEventLinkModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Character::class)]
    #[ORM\JoinColumn(name: 'character_id', referencedColumnName: 'id', nullable: false)]
    protected Character $character;

    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false)]
    protected Event $event;

    #[ORM\Column(type: 'integer')]
    protected int $points;
}
