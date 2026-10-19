<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'trans_no',
        'user_id',
        'supplier_id',
        'order_id',
        'payment_type',
        'payment_status',
        'amount',
        'transaction_date',
        'status', 
        'created_at', 
        'updated_at' 
    ];

    public function orders()
    {
        return $this->belongto(Order::class, 'order_id');
    }
}
