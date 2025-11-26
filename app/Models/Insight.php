<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insight extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'no',
        'name',
        'years',
        'conditions',
        'image',
    ];
    protected $dates = ['deleted_at'];
}
