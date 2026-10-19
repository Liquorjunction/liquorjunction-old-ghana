<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteSend extends Model
{
    use HasFactory;
    protected $table = 'quote_send';

    protected $fillable = [
        'send_id',
        'to_id',
        'quote_id',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
