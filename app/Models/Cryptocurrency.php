<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cryptocurrency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no',
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
    protected $dates = ['deleted_at'];
}
