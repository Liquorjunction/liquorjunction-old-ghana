<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    use HasFactory;


    protected $table = 'tbl_promocode';

    protected $fillable = [
        'id',
        'promo_name',
        'description',
        'start_date',
        'end_date',
        'image',
        'discount_percentage',
        'minimum_amount',
        'total_usage',
        // 'product_type',
        // 'category_id',
        // 'subcategory_id',
        // 'brand_id',
        // 'product_id',
        'allowed_time',
        'status',
        'created_at',
        'updated_at',
        'created_id',
        'updated_id',
    ];
}
