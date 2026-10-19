<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hamburger extends Model
{
    use HasFactory;
    protected $table = 'hamburger';

    protected $fillable = [
        'uniqid',
        'title',
        'image',
        'short_description',
        'long_description',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
