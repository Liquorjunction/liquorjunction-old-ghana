<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotedProduct extends Model
{
    use HasFactory;
    protected $table = 'promoted_product';

    protected $fillable = [
        'product_id',
        'category_id',
        'supplier_id',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
