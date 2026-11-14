<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreDetails extends Model
{
    use HasFactory;
    protected $table = 'store_details';

    protected $fillable = [
        'uniqid', 
        'sub_admin_type',
        'sub_admin_id',
        'store_name',
        'country', 
        'state', 
        'contact_number',
        'email',
        'zip_code', 
        'street_address', 
        'address', 
        'latitude', 
        'longitude', 
        'status'
    ];

    public function timings()
    {
        return $this->hasMany(StoreTimingWeek::class, 'store_id', 'id')
            ->leftJoin('week_list', 'week_list.id', '=', 'store_timing_week.week_id')
            ->select('store_timing_week.*', 'week_list.name as day_name')
            ->orderBy('store_timing_week.week_id', 'ASC');
    }
}
