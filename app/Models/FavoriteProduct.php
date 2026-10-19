<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteProduct extends Model
{
    use HasFactory;
    protected $table = 'favorite_product';

    protected $fillable = [
        'product_id',
        'user_id',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
