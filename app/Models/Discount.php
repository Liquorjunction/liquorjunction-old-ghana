<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discount'; 

    protected $fillable = [
        'discount_type',
        'discount_amount',
        'discount_percentage',
        'min_amount',
        // 'max_amount',
        'upto_amount',
        'expiry_date',
        'status',
    ];
}
