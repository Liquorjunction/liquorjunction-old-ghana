<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoints extends Model
{
    use HasFactory;
     protected $table = 'loyalty_points';

    protected $fillable = [
        'user_id',
        'order_id',
        'order_ref_id',
        'points',
        'type',
        'status',
        'description',
        'created_at', 
        'updated_at' 
    ];
}
