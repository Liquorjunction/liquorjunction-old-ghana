<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $table = 'labels';

    protected $fillable = [
        'label_name', 
        'label_value', 
        'label_type',
        'status'
    ];
}
