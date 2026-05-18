<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING_PAYMENT = 'PENDING_PAYMENT';
    case PAID_PROCESSING = 'PAID_PROCESSING';
    case PICKUP_REQUESTED = 'PICKUP_REQUESTED';
    case ON_DELIVERY = 'ON_DELIVERY';
    case DELIVERED = 'DELIVERED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
}
