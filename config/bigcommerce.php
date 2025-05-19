<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BigCommerce Order Status Mapping
    |--------------------------------------------------------------------------
    |
    | This array maps order status IDs to readable names. Use this to avoid
    | saving only raw IDs in the database or showing unreadable values in logs.
    | Source: https://developer.bigcommerce.com/docs/rest-management/orders/order-status
    |
    */

    'order_statuses' => [
        0  => 'Incomplete',
        1  => 'Pending',
        2  => 'Shipped',
        3  => 'Partially shipped',
        4  => 'Refunded',
        5  => 'Cancelled',
        6  => 'Declined',
        7  => 'Awaiting payment',
        8  => 'Awaiting pickup',
        9  => 'Awaiting shipment',
        10 => 'Completed',
        11 => 'Awaiting fulfillment',
        12 => 'Manual verification required',
        13 => 'Disputed',
        14 => 'Partially refunded',
    ],

    'processable_statuses' => [
        2,3,4,5,8,9,10,11,13,14,
    ],

    'ignored_statuses' => [
        0,1,6,7,12,
    ],

];
