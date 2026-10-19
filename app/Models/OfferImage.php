<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferImage extends Model
{
    use HasFactory;
     protected $table = 'offer_image';

    protected $fillable = [
        'offer_id',
        'image',
        'status', 
        'created_at', 
        'updated_at' 
    ];

    public function offer()
    {
        return $this->belongsTo(Offers::class);
    }
}
