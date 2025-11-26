<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessTaxi extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'no',
        'name',
        'image',
        'class',
        'resource',
        'income_per_hour',
        'price',
    ];
    protected $dates = ['deleted_at'];
}
