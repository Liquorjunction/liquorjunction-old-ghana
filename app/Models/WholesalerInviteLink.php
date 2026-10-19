<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholesalerInviteLink extends Model
{
    use HasFactory;
    protected $table = 'wholesaler_invite_link';

    protected $fillable = [
        'email',
        'status', 
        'created_at', 
        'updated_at' 
    ];
}
