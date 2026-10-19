<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeFrame extends Model
{
    use HasFactory;
    protected $table = 'time_frame';

    protected $fillable = [
        'name',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
