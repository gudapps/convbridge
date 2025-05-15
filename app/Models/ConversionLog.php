<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionLog extends Model
{
    protected $fillable = [
        'store_id',
        'order_id',
        'platform',
        'status',
        'response',
        'sent_at',
    ];

    protected $dates = [
        'sent_at',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function trackedOrder()
    {
        return $this->belongsTo(TrackedOrder::class, 'order_id', 'order_id');
    }
}
