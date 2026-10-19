<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTimingWeek extends Model
{
    use HasFactory;

    protected $table = 'store_timing_week';

    protected $fillable = [
        'store_id', 
        'week_id', 
        'start_time', 
        'end_time', 
        'status'
    ];
}
