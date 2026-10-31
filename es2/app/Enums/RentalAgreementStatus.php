<?php

namespace App\Enums;

enum RentalAgreementStatus: string
{
    case PENDING = 'PENDING';
    case IN_TRANSIT = 'IN_TRANSIT';

    case RECEIVED = 'RECEIVED';
    case RETURNED = 'RETURNED';

    case CANCELLED = 'CANCELLED';

}
