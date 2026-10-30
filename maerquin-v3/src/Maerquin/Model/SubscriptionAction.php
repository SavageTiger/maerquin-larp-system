<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Model;

enum SubscriptionAction: string
{
    case Confirm = 'confirm';
    case PlaceOnWaitingList = 'place_on_waiting_list';
    case ResetStatus = 'reset_status';
    case MarkAsPaid = 'mark_as_paid';
    case MarkAsUnpaid = 'mark_as_unpaid';
}
