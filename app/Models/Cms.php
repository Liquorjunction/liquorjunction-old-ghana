<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
    protected $table = 'cms';

    protected $fillable = [
        'page_name', 
        'page_content',
        'mobile_page_content',
        'title',
        'body',
        'image',
        'create_by',
        'update_by',
        'status'
    ];
}
