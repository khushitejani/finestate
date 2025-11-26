<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YatchShop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['no', 'name', 'description', 'price', 'image'];
    protected $dates = ['deleted_at'];
}
