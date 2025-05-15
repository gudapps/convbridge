<?php

namespace App\Jobs\BigCommerce;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchAndStoreOrderJob implements ShouldQueue
{
    use Queueable;

    protected $storeHash;
    protected $orderId;

    public function __construct($storeHash, $orderId)
    {
        $this->storeHash = $storeHash;
        $this->orderId = $orderId;
    }

    public function handle(): void
    {
        //

        exit;
    }
}
