<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case MARKETING = 'marketing';
    case SYSTEM = 'system';
}