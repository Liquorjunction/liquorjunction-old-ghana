<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';

    protected $fillable = [
        'language_id', 
        'question_name',
        'question_name_fr', 
        'answer', 
        'answer_fr',
        'create_by', 
        'update_by', 
        'status'
    ];

    public function FaqLanguage()
    {
      return $this->hasOne('App\Models\Language','id','language_id');
    }
}
