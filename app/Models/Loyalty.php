<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loyalty extends Model
{
    use HasFactory;
     protected $table = 'loyalty';

    protected $fillable = [
        'uniqid',
        'minimum_purchase_amount',
        'loyalty_percentage',
        'maximum_points',
        'points_per_ghs',
        'redeem_ghs_value',
        'max_redeem_percentage',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
