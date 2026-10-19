<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariants extends Model
{
    use HasFactory;
    protected $table= 'product_variants';
    protected $fillable = [
        'product_id',
        'variant_size',
        'variant_uof',
        'variant_price',
        'variant_discounted_price',
        'variant_qty',
        'sold_qty',
        'available_qty',
        'packets',
        'comment',
        'status',

    ];

    public function get_product_details() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function get_uof_data() {
        return $this->belongsTo(Uofs::class, 'variant_uof');
    }

    public static function getProductDetalsBasedOnVariant(){
        $query =  ProductVariants::withWhereHas('get_product_details', function ($query) {
                        $query->withWhereHas('get_category', function ($query) {
                                $query->where('status', 1);
                            })->withWhereHas('get_subcategory', function ($query) {
                                $query->where('status', 1);
                            })->withWhereHas('get_brand_details', function ($query) {
                                $query->where('status', 1);
                            })->withWhereHas('get_product_images', function ($query) {
                                $query->where('status', 1);
                            })->where('status', 1);
                    })->where('status', 1); 
        return $query;
    }

    public function cart() {
        return $this->hasMany(Cart::class, 'product_variant_id');
    }
}
