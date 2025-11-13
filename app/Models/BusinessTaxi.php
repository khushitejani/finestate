<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessTaxi extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'class',
        'resource',
        'income_per_hour',
        'price',
    ];
}
