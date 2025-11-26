<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Share extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'no',
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
    protected $dates = ['deleted_at'];
}
