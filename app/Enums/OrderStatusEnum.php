<?php

namespace App\Enums;

enum OrderStatusEnum: int
{
    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;
    case CANCELLED = 4;
    case ORDERED_FROM_SUPPLIER = 5;
    case CANCELLED_FROM_SUPPLIER = 6;
    case DELIVERED_FROM_SUPPLIER = 7;
    case SENT_TO_WAREHOUSE = 8;
    case DELIVERED_TO_WAREHOUSE = 9;

    public static function isAllowedToEditByNonAdmin(int $status): bool
    {
        return $status >= self::PENDING->value && $status < self::APPROVED->value;
    }

}
