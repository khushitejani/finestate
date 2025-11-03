<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'image',
        'income_per_hour',
        'address',
        'price'
    ];
    protected $casts = [
        'image' => 'array',
    ];
    public function getFirstImageAttribute()
    {
        return $this->image[0] ?? null;
    }
}
