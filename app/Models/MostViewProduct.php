<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MostViewProduct extends Model
{
    use HasFactory;

    protected $table = 'most_viewed_product';

    protected $fillable = [
        'product_id',
        'count',
        'status', 
        'created_at', 
        'updated_at' 
    ];

    public function product() {
        return $this->hasOne(Product::class, 'id','product_id');        
    }

    public static function getMostViewProductSubcategoryIds(){
        $query = MostViewProduct::withWhereHas('product',function($query){
            $query->where('status','1');
        })->orderBy('count','DESC')->get();
        $subcategory_ids = array();
        foreach($query as $result){
            $subcategory_ids[] = $result->product->subcategory_id;
        }
        $subcategory_ids = array_unique($subcategory_ids);
        
        return $subcategory_ids;

    }

    
}
