<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

enum PaymentState: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
}
