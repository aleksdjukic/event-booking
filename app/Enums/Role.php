<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case ORGANIZER = 'organizer';
    case CUSTOMER = 'customer';
}
