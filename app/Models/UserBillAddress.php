<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBillAddress extends Model
{
    use HasFactory;
    protected $table = 'user_bill_address';

    protected $fillable = [
        'user_id',
        'name',
        'phonecode',
        'phone',
        'address',
        'country_id',
        'region_id',
        'area_id',
        'city',
        'zip_code',
        'status', 
        'created_at', 
        'updated_at' 
    ];          

    public function country()
    {
        return $this->hasOne('App\Models\Country', 'id', 'country_id');
    }

    public function region()
    {
        return $this->hasOne('App\Models\Region', 'id', 'region_id');
    }

    public function area()
    {
        return $this->hasOne('App\Models\Area', 'id', 'area_id');
    }
}
