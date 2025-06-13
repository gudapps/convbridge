<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackedOrderAddition extends Model
{
    protected $fillable = [
        'order_id',
        'fbp',
        'fbc',
        'user_agent',
    ];

    public function order()
    {
        return $this->belongsTo(TrackedOrder::class, 'order_id', 'order_id');
    }
}
