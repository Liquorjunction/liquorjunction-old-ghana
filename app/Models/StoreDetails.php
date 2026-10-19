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
        'store_name_fr',
        'country', 
        'state', 
        'contact_number',
        'zip_code', 
        'street_address', 
        'address', 
        'latitude', 
        'longitude', 
        'status'
    ];
}
