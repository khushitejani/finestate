<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected $fillable = [
        'name',
        'image',
        'share_price',
        'dividend',
        'time_period',
        'capitalization',
        'available_shares',
        'day_prices'
    ];
    protected $casts = [
        'day_prices' => 'array',
    ];
}
