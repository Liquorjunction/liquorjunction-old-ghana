<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'level';

    protected $fillable = [
        'title',
        'status',
        'created_at',
        'updated_at'
    ];
}
