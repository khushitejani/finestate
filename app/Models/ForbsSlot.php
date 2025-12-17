<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForbsSlot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'no',
        'name',
        'business',
        'price',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
