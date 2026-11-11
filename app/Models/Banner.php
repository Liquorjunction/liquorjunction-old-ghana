<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'title',
        'type',
        'category_id',
        'subcategory_id',
        'product_id',
        'brand_id',
        'description',
        'banner_url',
        'photo',
        'offer',
        'text_color',
        'highlight',
        'status' 
    ];
}
