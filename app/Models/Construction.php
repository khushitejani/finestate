<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Construction extends Model
{
    use SoftDeletes; 

    protected $fillable = [
        'no',
        'name',
        'metal',
        'builder_men',
        'wood',
        'concrete',
        'total_cost_of_construction',
        'total_profit',
        'total_percentage',
        'time',
        'image',
        'total_return_after_completion',
    ];

    protected $casts = [
        'total_cost_of_construction' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'total_percentage' => 'decimal:2',
        'total_return_after_completion' => 'decimal:2',
    ];
}
