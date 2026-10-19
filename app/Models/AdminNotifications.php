<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotifications extends Model
{
    use HasFactory;

    protected $table = 'admin_notifications';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'notification_type',
        'title',
        'message', 
        'is_read', 
        'created_at', 
        'updated_at' 
    ];
}
