<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    use HasFactory;

    protected $table = 'order_tracking';

    protected $fillable = [
        'uniqid',
        'order_status',
        'change_date',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
