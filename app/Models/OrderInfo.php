<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInfo extends Model
{
    use HasFactory;

    protected $table = 'order_info';
  

    protected $fillable = [
            'order_id',
            'country_id',
            'region_id',
            'area_id',
            'customer_name',
            'customer_mobile',
            'customer_email',
            'customer_country',
            'promocode_name',
            'promocode_percentage',
            'order_from',
            'store_pickup_address',
            'delivery_fee',
            'created_at',
            'updated_at'
     ];
    
    public function orders()
    {
        return $this->belongto(Order::class, 'order_id');
    }
}
