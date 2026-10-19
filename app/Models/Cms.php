<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
    protected $table = 'cms';

    protected $fillable = [
        'language_id',
        'page_name', 
        'page_content',
        'page_content_fr',
        'mobile_page_content',
        'mobile_page_content_fr',
        'title',
        'body',
        'image',
        'create_by',
        'update_by',
        'status'
    ];

    public function CmsLanguage()
    {
      return $this->hasOne('App\Models\Language','id','language_id');
    }
}
