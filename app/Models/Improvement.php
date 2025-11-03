<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Improvement extends Model
{
    protected $fillable = ['image', 'name', 'price'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
