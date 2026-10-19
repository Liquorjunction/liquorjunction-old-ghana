<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'language_id',
        'title',
        'title_fr',
        'type',
        'category_id',
        'subcategory_id',
        'product_id',
        'brand_id',
        'description',
        'description_fr',
        'banner_url',
        'photo',
        'offer',
        'text_color',
        'highlight',
        'status' 
    ];

    public function LabelLanguage()
    {
      return $this->hasOne('App\Models\Language','id','language_id');
    }
}
