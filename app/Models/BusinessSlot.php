<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessSlot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no',
        'expansion_time',
        'price'
    ];
}
