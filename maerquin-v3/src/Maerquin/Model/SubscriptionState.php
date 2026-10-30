<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

enum SubscriptionState: string
{
    case Pending = 'PENDING';
    case Open = 'OPEN';
    case Closed = 'CLOSED';
    case Expired = 'EXPIRED';
}
