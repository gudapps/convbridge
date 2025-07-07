<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackedOrderItem extends Model
{
    protected $fillable = [
        'tracked_order_id',
        'product_id',
        'sku',
        'name',
        'quantity',
        'price',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function trackedOrder()
    {
        return $this->belongsTo(TrackedOrder::class);
    }
}
