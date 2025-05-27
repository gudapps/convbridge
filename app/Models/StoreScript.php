<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreScript extends Model
{
    protected $fillable = ['store_id', 'provider', 'event_type', 'script_uuid'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
