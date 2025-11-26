<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Improvement extends Model
{
    use  SoftDeletes;
    protected $fillable = ['no', 'image', 'name', 'price'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    protected $dates = ['deleted_at'];
}
