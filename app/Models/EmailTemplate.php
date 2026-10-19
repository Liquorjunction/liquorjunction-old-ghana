<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'emailtemplates';

    protected $fillable = [
        'language_id',
        'title',
        'subject', 
        'content', 
        'status' 
    ];

    public function EmailTemplateLanguage()
    {
      return $this->hasOne('App\Models\Language','id','language_id');
    }
}
