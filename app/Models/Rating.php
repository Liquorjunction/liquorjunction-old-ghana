<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = "ratings";

    protected $fillable = [ 'user_id',  'product_id','ratings','review','status','created_at'];

    public function product() {
        return $this->belongTo(Product::class, 'id', 'product_id');
    }

    public function userRating() {
        return $this->hasMany(MainUser::class, 'id', 'user_id');
    }

  
}
