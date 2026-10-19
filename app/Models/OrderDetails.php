<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_detail';

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'variant_size',
        'variant_price',
        'paid_price',
        'quantity',
        'supplier_id',
        'status',
        'created_at',
        'updated_at'
    ];

    public function get_product()
    {
        return $this->hasOne(Product::class, 'product_id');
    }

    // public function product()
    // {
    //     return $this->hasMany(Product::class, 'product_id');
    // }

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_id');
    }
    public function product()
    {
        return $this->hasOne(Product::class, 'product_id');
    }
            

}
