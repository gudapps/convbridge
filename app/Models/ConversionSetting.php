<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionSetting extends Model
{
    protected $fillable = [
        'store_id',
        'platform',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
