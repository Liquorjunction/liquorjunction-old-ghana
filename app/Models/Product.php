<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use DB;

class Product extends Model
{
    use HasFactory;
    protected $table = 'product';

    protected $fillable = [
        'uniqid',
        'supplier_id',
        'product_item_id',
        'product_name',
        'short_description',
        'description',
        'category_id',
        'tech_data_sheet',
        'product_image',
        'retail_price',
        'in_online',
        'in_store',
        'discount_price',
        'is_admin_approve',
        'status', 
        'created_at', 
        'updated_at',
        'subcategory_id',
        'brand_id',
        'video',
        'sku',
        'product_qty',
    ];

    public function get_category() {
        return $this->belongsTo(Categories::class, 'category_id');
    }
    public function get_subcategory() {
        return $this->belongsTo(SubCategories::class, 'subcategory_id');
    }
    public function get_product_images() {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
    public function get_brand_details() {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function get_product_variants() {
        return $this->hasMany(ProductVariants::class, 'product_id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'product_id');
    }
    public function product_rating() {
        return $this->hasMany(Rating::class, 'product_id');
    }

    public function low_price_product() {
        return $this->hasOne(ProductVariants::class, 'product_id')->where('status',1)->orderBy('variant_discounted_price','asc');
    }

    public function variant_product_price() {
        return $this->hasOne(ProductVariants::class, 'product_id')->where('status',1);
    }

    public static function productUserRating($id){
        $query = DB::table('product')
        ->join('ratings','product.id','=','ratings.product_id')->join('main_users','main_users.id','=','ratings.user_id')->select('product.id','ratings.*','main_users.first_name','main_users.last_name')->where('product.id',$id)->get();
        return $query;
    }

    /*---This function is checking all required relation table data status.
    This function is using everywhere for fetching data, so DONOT change the this. */
    public static function activeProductsBasedOnRelations(){
        $product = Product::withWhereHas('get_product_images', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_category', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_subcategory', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_brand_details', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_product_variants', function ($query) {           
            $query->where('status', 1);            
        })->where('status',1);
        return $product;
    }
    /*---This function is using for getting min price from product variant.
    It is using on product details page for price range. */
    public function getproductMinPrice($category_id = null, $subcategory_id = null, $brand_id = null)
    {
        $query = \DB::table('product')
            ->join('product_variants', 'product.id', '=', 'product_variants.product_id')
            ->where('product.status', 1)
            ->where('product_variants.status', 1)
            ->where('product_variants.variant_price', '>', 0);

        if (!empty($category_id)) {
            $query->where('product.category_id', $category_id);
        }
        if (!empty($subcategory_id)) {
            $query->where('product.subcategory_id', $subcategory_id);
        }
        if (!empty($brand_id)) {
            $query->where('product.brand_id', $brand_id);
        }

        $min = $query->min('product_variants.variant_price');

        // Fallback to global active min price if no products found in filter
        if ($min === null && (!empty($category_id) || !empty($subcategory_id) || !empty($brand_id))) {
            $min = \DB::table('product')
                ->join('product_variants', 'product.id', '=', 'product_variants.product_id')
                ->where('product.status', 1)
                ->where('product_variants.status', 1)
                ->where('product_variants.variant_price', '>', 0)
                ->min('product_variants.variant_price');
        }

        $obj = new \stdClass();
        $obj->min_price = $min !== null ? floor((float) $min) : 0;
        return $obj;
    }

    /*---This function is using for getting max price from product variant.
    It is using on product details page for price range. */
    public function getproductMaxPrice($category_id = null, $subcategory_id = null, $brand_id = null)
    {
        $query = \DB::table('product')
            ->join('product_variants', 'product.id', '=', 'product_variants.product_id')
            ->where('product.status', 1)
            ->where('product_variants.status', 1);

        if (!empty($category_id)) {
            $query->where('product.category_id', $category_id);
        }
        if (!empty($subcategory_id)) {
            $query->where('product.subcategory_id', $subcategory_id);
        }
        if (!empty($brand_id)) {
            $query->where('product.brand_id', $brand_id);
        }

        $max = $query->max('product_variants.variant_price');

        // Fallback to global active max price if no products found in filter
        if ($max === null && (!empty($category_id) || !empty($subcategory_id) || !empty($brand_id))) {
            $max = \DB::table('product')
                ->join('product_variants', 'product.id', '=', 'product_variants.product_id')
                ->where('product.status', 1)
                ->where('product_variants.status', 1)
                ->max('product_variants.variant_price');
        }

        $obj = new \stdClass();
        $obj->max_price = $max !== null ? ceil((float) $max) : 0;
        return $obj;
    }

    public function mostViewProduct(){
        return $this->hasOne(MostViewProduct::class, 'product_id');
    }
    
   

}
