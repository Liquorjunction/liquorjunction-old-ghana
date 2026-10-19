<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;
    protected $table = 'quote';

    protected $fillable = [
        'category_id',
        'time_frame_id',
        'material_id',
        'user_id',
        'assign_user_id',
        'post_code',
        'description',
        'quote_image',
        'quote_status', 
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
