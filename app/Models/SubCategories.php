<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategories extends Model
{
    use HasFactory;
    protected $table = 'sub_categories';

    protected $fillable = [
        'uniqid',
        'category_id',
        'title',
        'title_fr',
        'description',
        'description_fr',
        'image',
        'status', 
        'created_at', 
        'updated_at' 
    ];

   public function category() {
        return $this->hasOne(Categories::class, 'id', 'category_id');
    }
}
