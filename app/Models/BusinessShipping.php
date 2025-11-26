<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessShipping extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'no',
        'name',
        'category',
        'price',
        'income_per_hour',
        'image',
        'resource'
    ];
    protected $dates = ['deleted_at'];
}
