<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
    protected $table = 'user_address';

    protected $fillable = [
        'user_id',
        'name',
        'phonecode',
        'phone',
        'address',
        'country_id',
        'region_id',
        'area_id',
        'state',
        'city',
        'zip_code',
        'billing_address',
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
