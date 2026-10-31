<?php

namespace App\Enums;

enum PurchaseOrderStatus : string
{
    case PENDING = 'PENDING';
    case IN_TRANSIT = 'IN_TRANSIT';
    case RECEIVED = 'RECEIVED';
    case CANCELLED = 'CANCELLED';
}
