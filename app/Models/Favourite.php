<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $table = 'favourite';

    protected $fillable = [
        'user_id', 
        'class_id', 
        'status'
    ];

    public function GetUser()
    {
      return $this->hasOne('App\Models\MainUser','id','user_id');
    }
}
