<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

enum SubscriptionStatus: string
{
    case Received = 'received';
    case Confirmed = 'confirmed';
    case WaitingList = 'waiting_list';
}
