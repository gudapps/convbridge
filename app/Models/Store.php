<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'store_hash',
        'access_token',
        'scope',
        'context',
        'store_url',
        'store_name',
        'store_data',
    ];

    // One store has many conversion settings (Facebook, Google, etc.)
    public function conversionSettings()
    {
        return $this->hasMany(ConversionSetting::class);
    }

    // One store has many tracked orders
    public function trackedOrders()
    {
        return $this->hasMany(TrackedOrder::class);
    }

    // One store has many tracked customers
    public function trackedCustomers()
    {
        return $this->hasMany(TrackedCustomer::class);
    }

    // One store has many conversion logs
    public function conversionLogs()
    {
        return $this->hasMany(ConversionLog::class);
    }

    protected $casts = [
        'store_data' => 'array',
    ];
}
