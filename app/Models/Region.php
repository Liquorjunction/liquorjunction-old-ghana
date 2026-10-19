<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = "regions";

    protected $fillable = [
        'id',
        'country_id',
        'title',
        'title_fr',
        'status', 
        'created_at', 
        'updated_at'
         
    ];

    public function country() {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
    
}
