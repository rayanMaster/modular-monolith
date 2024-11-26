<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ADMIN = 'admin';
    case STORE_KEEPER = 'store_keeper';
    case SITE_MANAGER = 'site_manager';
    case WORKER = 'worker';

}
