<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{

    use HasFactory;
    protected $table = 'order';

    protected $fillable = [
        'uniqid',
        'transaction_id',
        'supplier_id',
        'order_id',
        'delivery_address_id',
        'delivery_address',
        'billing_address',
        'isSameAddress',
        'order_type',
        'total_amount',
        'cart_discount',
        'payable_amount',
        'order_date',
        'recipientName',
        'giftMessage',
        'delivery_options',
        'delivery_instructions',
        'shipping_method',
        'loyalty_point_id',
        'order_time',
        'order_status',
        'cancelled_by',
        'cancelled_user',
        'status',
        'is_stock_updated', 
        'user_id',
        'created_at', 
        'updated_at' 
    ];

    public function get_order_details() {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }
    // public function get_product() {
    //     return $this->hasOne(product::class,'product_id');
    // }
   
    public function order_details()
    {
        return $this->hasMany(OrderDetails::class,'order_id');
    }

    public function orderInfo()
    {
        return $this->hasOne(OrderInfo::class,'order_id');
    }

    public function transcations()
    {
        return $this->hasOne(Transactions::class,'order_id');
    }
    
    public function user()
    {
        return $this->belongsTo(MainUser::class);
    }
    
    public static function getTopSellingProduct(){
        $top_product_count = DB::table('order_detail')->leftjoin('order','order.id','=','order_detail.order_id')->selectRaw('product_id, count(product_id) as number_of_product')->groupBy('product_id')->where('order.status','!=',4)->orderBy('number_of_product','desc')->get();
        return $top_product_count;
    }
}       
