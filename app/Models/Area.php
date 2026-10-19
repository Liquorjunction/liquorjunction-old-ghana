<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = "area";

    protected $fillable = [
        'id',
        'region_id',
        'title',
        'title_fr',
        'delivery_fee',
        'delivery_amount',
        'status', 
        'created_at', 
        'updated_at'
         
    ];

   public function region() {
    return $this->hasOne(Region::class, 'id', 'region_id');
   }
}   
