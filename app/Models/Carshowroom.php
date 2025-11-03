<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carshowroom extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'images',
    ];
    protected $casts = [
        'images' => 'array',
    ];
}
