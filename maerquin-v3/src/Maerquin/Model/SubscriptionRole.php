<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

enum SubscriptionRole: string
{
    case Player = 'player';
    case Npc = 'npc';
}
