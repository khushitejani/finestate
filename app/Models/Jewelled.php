<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jewelled extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'no',
        'name',
        'price',
        'image',
    ];
    protected $dates = ['deleted_at'];
}
