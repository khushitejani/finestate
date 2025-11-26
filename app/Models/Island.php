<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Island extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'no',
        'name',
        'image',
        'price',
        'description',
    ];
     protected $dates = ['deleted_at'];
}
