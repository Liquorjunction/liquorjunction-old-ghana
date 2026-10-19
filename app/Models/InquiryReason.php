<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryReason extends Model
{
    use HasFactory;

    protected $table = 'inquiry_reason';

    protected $fillable = [
        'title', 
        'title_fr', 
        'status'
    ];
}
