<?php

namespace App\Enums;

enum StockState: string
{
    case IN_WAREHOUSE = 'in_warehouse';
    case AT_EVENT = 'at_event';
    case IN_TRANSIT_TO_EVENT = 'in_transit_to_event';
    case IN_TRANSIT_FROM_EVENT = 'in_transit_from_event';
    case CONSUMED = 'consumed';
    case LOST = 'lost';
    case RETURNED_TO_SUPPLIER = 'returned_to_supplier';
}