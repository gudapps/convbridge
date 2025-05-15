<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackedCustomer extends Model
{
    protected $fillable = [
        'store_id',
        'tracked_order_id',
        'customer_id',
        'email',
        'first_name',
        'last_name',
        'phone',
        'country_code',
        'region',
        'city',
        'zip',
        'address',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function trackedOrder()
    {
        return $this->belongsTo(TrackedOrder::class);
    }
}
