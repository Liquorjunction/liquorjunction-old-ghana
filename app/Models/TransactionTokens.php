<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionTokens extends Model
{
    protected $table = 'transaction_token';

    // Fields that can be mass-assigned
    protected $fillable = [
        'order_id',
        'transaction_token',
    ];

    const UPDATED_AT = null;
}
