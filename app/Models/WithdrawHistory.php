<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawHistory extends Model
{
    use HasFactory;

    protected $table = 'withdraw_history';

    protected $fillable = [
        'instructor_id', 
        'amount',
        'balance',
        'status'
    ];

    public function GetUser()
    {
      return $this->hasOne('App\Models\MainUser','id','instructor_id');
    }
}
