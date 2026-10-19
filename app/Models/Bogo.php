<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bogo extends Model
{
    use HasFactory;


    protected $table = 'bogo';

    protected $fillable = [
        'id',
        'start_date',
        'end_date',
        'product_type',
        'category_id',
        'subcategory_id',
        'brand_id',
        'product_id',
        'status',
        'created_at',
        'updated_at',
        'created_id',
        'updated_id',
    ];
}
