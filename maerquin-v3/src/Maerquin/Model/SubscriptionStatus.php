<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

enum SubscriptionStatus: string
{
    case PendingPayment = 'pending_payment';
}
