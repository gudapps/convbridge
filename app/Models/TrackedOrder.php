<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackedOrder extends Model
{
    protected $fillable = [
        'store_id',
        'order_id',
        'status',
        'status_id',
        'order_data',
    ];

    protected $casts = [
        'order_data' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(TrackedOrderItem::class);
    }

    public function customer()
    {
        return $this->hasOne(TrackedCustomer::class);
    }

    public function conversionLogs()
    {
        return $this->hasMany(ConversionLog::class, 'order_id', 'order_id');
    }

    public function additions()
    {
        return $this->hasOne(TrackedOrderAddition::class, 'order_id', 'order_id');
    }
}
