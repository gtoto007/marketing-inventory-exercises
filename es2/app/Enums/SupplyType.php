<?php

namespace App\Enums;

enum SupplyType: string
{
    case PURCHASABLE = 'PURCHASABLE';
    case RENTABLE = 'RENTABLE';
    case BOTH = 'BOTH';
}