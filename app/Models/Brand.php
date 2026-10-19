<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'brand';

    protected $fillable = [
        'id',
        'title',
        'title_fr',
        'status', 
        'created_at', 
        'updated_at' 
    ];

    public function get_products() {
        return $this->hasOne(Product::class, 'brand_id');
    }
}
