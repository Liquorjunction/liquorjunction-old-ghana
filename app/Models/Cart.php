<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';

    protected $fillable = [
        'uniqid',
        'product_id',
        'product_variant_id', 
        'supplier_id',
        'user_id',
        'quantity',
        'total_price',
        'product_price',
        'offer_price',
        'delivery_address_id',
        'order_type',
        'is_bogo',
        'is_offer',
        'discount_amount',
        'offer_type',
        'status', 
        'created_at', 
        'updated_at' 
    ];

    public function product_variants() {
        return $this->hasOne(ProductVariants::class, 'id');
    }

    public static function getProductBasedOnVariant(){
        $query = cart::withWhereHas('product_variants',function ($query) {
                    $query->withWhereHas('get_product_details', function ($query) {
                        $query->withWhereHas('get_category', function ($query) {
                                $query->where('status', 1);
                            })->withWhereHas('get_subcategory', function ($query) {
                                $query->where('status', 1);
                            })->withWhereHas('get_brand_details', function ($query) {
                                $query->where('status', 1);
                            })->withWhereHas('get_product_images', function ($query) {
                                $query->where('status', 1);
                            });
                    })->where('status', 1); 
        })->where('status', 1);
        return $query;
    }

}
