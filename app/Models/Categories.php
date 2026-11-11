<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
   
    protected $table = 'categories';

    protected $fillable = [
        'uniqid',
        'icon_id',
        'title',
        'description',
        'url',
        'photo',
        'imagefile',
        'status', 
        'created_at', 
        'updated_at' 
    ];

    public function get_products() {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function subcategory() {
        return $this->hasMany(SubCategories::class, 'category_id');
    }
     
}
