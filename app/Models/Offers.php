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
        'start_date',
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

    public function brand() {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category() {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function subcategory() {
        return $this->belongsTo(SubCategories::class, 'subcategory_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
