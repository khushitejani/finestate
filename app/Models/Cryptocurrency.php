<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Cryptocurrency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'image',
        'price',
        'cryptocurrencies_cap',
        'available_for_purchase',
        'day_prices',
    ];
    protected $casts = [
        'day_prices' => 'array',
    ];
}
