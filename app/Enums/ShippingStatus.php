<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case AWAITING_PICKUP = 'AWAITING_PICKUP';
    case PICKED_UP = 'PICKED_UP';
    case ON_DELIVERY = 'ON_DELIVERY';
    case DELIVERED = 'DELIVERED';
}
