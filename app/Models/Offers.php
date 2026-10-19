<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offers extends Model
{
    use HasFactory;

    protected $table = 'offers'; 

    protected $fillable = [
        'offer_type',
        'dis_amount',
        // 'min_amount',
        // 'max_amount',
        'expiry_date',
        // 'total_usage',
        // 'max_users',
        'product_type',
        'category_id',
        'subcategory_id',
        'brand_id',
        'product_id',
        'photo',
        'title',
        'template',
        'custom_url',
        'description',
        'status',
    ];

    public function get_offer_images() {
        return $this->hasMany(OfferImage::class, 'offer_id');
    }
}
