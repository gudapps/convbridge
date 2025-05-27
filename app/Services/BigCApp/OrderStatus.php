<?php

namespace App\Services\BigCApp;

class OrderStatus
{
    public static function isProcessable(int $statusId): bool
    {
        return in_array($statusId, config('bigcommerce.processable_statuses'));
    }
}
