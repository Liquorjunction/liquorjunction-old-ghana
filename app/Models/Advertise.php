<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertise extends Model
{
    use HasFactory;
     protected $table = 'advertise';

    protected $fillable = [
        'uniqid',
        'wholesaler_id',
        'title',
        'description',
        'image',
        'mobile_banner',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
