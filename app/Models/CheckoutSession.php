<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutSession extends Model
{
    use HasFactory;
    protected $table = 'checkout_sessions ';

    protected $fillable = [
        'user_id ',
        'apply_order'
    ];
   
}
