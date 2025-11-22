<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessShipping extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'income_per_hour',
        'image',
        'resource'
    ];
}
