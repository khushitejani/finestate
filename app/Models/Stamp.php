<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'years',
        'price',
        'image',
    ];
}
