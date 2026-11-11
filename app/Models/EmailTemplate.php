<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'emailtemplates';

    protected $fillable = [
        'title',
        'subject', 
        'content', 
        'status' 
    ];
}
