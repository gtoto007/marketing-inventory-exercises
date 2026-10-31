<?php

namespace App\Enums;

enum OwnershipType: string
{
    case OWNED = 'OWNED';
    case RENTED = 'RENTED';
}
