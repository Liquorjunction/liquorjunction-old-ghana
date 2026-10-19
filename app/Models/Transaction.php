<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{

    protected $table = 'transactions';

    protected $fillable = [
        'trans_no',
        'user_id',
        'service_provider_id',
        'order_id',
        'booking_id',
        'card_number',
        'cvc',
        'exp_month',
        'exp_year',
        'amount',
        'transaction_date',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
