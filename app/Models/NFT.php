<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFT extends Model
{
    use HasFactory;

    protected $table = 'nfts';

    protected $fillable = [
        'name',
        'image',
        'price',
        'description',
    ];
}
