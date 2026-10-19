<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersPayments extends Model
{
    use HasFactory;

    protected $table = 'users_payments';

    protected $fillable = [
        'user_id',
        'supplier_id',
        'order_id',
        'transaction_id',
        'payment_mode',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
