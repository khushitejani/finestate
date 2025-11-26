<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Property extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'no',
        'image',
        'income_per_hour',
        'address',
        'price'
    ];
    protected $casts = [
        'image' => 'array',
    ];
    protected $dates = ['deleted_at'];
    public function getFirstImageAttribute()
    {
        return $this->image[0] ?? null;
    }
}
