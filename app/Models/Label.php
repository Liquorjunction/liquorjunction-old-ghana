<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $table = 'labels';

    protected $fillable = [
        'language_id', 
        'label_name', 
        'label_value', 
        'label_value_fr', 
        'label_type',
        'status'
    ];

    public function LabelLanguage()
    {
      return $this->hasOne('App\Models\Language','id','language_id');
    }
}
