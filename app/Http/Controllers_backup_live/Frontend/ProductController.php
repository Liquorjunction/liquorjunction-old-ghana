<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Session\Store;
use Illuminate\Validation\Rule;
use App\Models\Cart;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\FavoriteProduct;
use App\Models\MostViewProduct;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariants;
use App\Models\Setting;
use App\Models\SubCategories;
use App\Models\Uofs;
use Auth;
use DB;
use Carbon\Carbon;
use Helper;
use Redirect;
use Mail;
use Session;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Offers;
use App\Models\Bogo;

class ProductController extends Controller
{    
    public function __construct()
    {        
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';       
    }

    public function listOld(Request $request,$id)
    {
        $category_id = base64_decode($request->id);
        $subcategory_id = base64_decode($request->sid);

        $user_id = $this->user_id;
        $product_val =  new Product;
        $product_max_price = $product_val->getproductMaxPrice();
        $product_min_price = $product_val->getproductMinPrice();
        
        $categoryDetails = Categories::where('id',$category_id)->where('status',1)->first();
        // dd($categoryDetails);
        $subCategoryDetails = SubCategories::where('id',$subcategory_id)->where('status',1)->where('category_id',$category_id)->first();
        if((empty($categoryDetails) && $request->sid=='') || (  $request->sid!='' && empty($subCategoryDetails) )){
            Alert::error('Error', __(Helper::language('something_went_wrong')));
            return redirect('/');
        }
        $categories = Categories::with(['subcategory'=>function ($query){
            $query->where('status',1);
        }])->where('status',1)->get();
        $brandData = Brand::where('status',1)->orderBy('title','asc')->get();

        
        $brand = array();
        $category = array();    
        $subcategory = array();
        $minprice = "";
        $maxprice = "";
        $products = Product::withWhereHas('get_product_images', function ($query){
            $query->where('status', 1);
        })->withWhereHas('get_category', function ($query)  use ($category, $category_id) {
            $query->where('status', 1);
            $query->when(($category && $category != null), function ($q) use ($category) {
                return $q->whereIn('id', $category);
            });
            $query->when(($category_id && $category_id != null), function ($q) use ($category_id) {
                return $q->where('id', $category_id);
            });
        })->withWhereHas('get_subcategory', function ($query) use ($subcategory, $subcategory_id){
            $query->where('status', 1);
            $query->when(($subcategory && $subcategory != null), function ($q) use ($subcategory) {
                return $q->whereIn('id', $subcategory);
            });
            $query->when(($subcategory_id && $subcategory_id != null), function ($q) use ($subcategory_id) {
                return $q->where('id', $subcategory_id);
            });
        })->withWhereHas('get_brand_details', function ($query) use ($brand) {
            $query->where('status', 1);
            $query->when(($brand && $brand != null), function ($q) use ($brand) {
                return $q->whereIn('id', $brand);
            });
        })->withWhereHas('low_price_product', function ($query) use ($minprice,$maxprice) {
            $query->when(($minprice && $minprice != null) && ($maxprice && $maxprice != null), function ($q) use ($minprice,$maxprice) {                
                return $q->whereBetween('variant_discounted_price', [$minprice, $maxprice]);
            });
        })->where('status',1);
      
        $limit = 16;
        $total_product_count = count($products->get());
        $showing_product_count = 0;
        if($total_product_count!=0 && $total_product_count >= $limit ){
            $showing_product_count = $limit;
        }elseif($total_product_count <= $limit ){
            $showing_product_count = $total_product_count;
        }
        $productData = $products->orderBy('product.id', 'desc')->limit($limit)->get();

        return view("frontend.product.product-list",compact('categoryDetails','user_id','productData','brandData','categories','product_max_price','product_min_price','subCategoryDetails','total_product_count','showing_product_count'));
    }
    
    public function list(Request $request,$id)
    {
        $category_id = base64_decode($request->id);
        $subcategory_id = base64_decode($request->sid);

        $user_id = $this->user_id;
        $product_val =  new Product;
        $product_max_price = $product_val->getproductMaxPrice();
        $product_min_price = $product_val->getproductMinPrice();
        
        $categoryDetails = Categories::where('id',$category_id)->where('status',1)->first();
        $subCategoryDetails = SubCategories::where('id',$subcategory_id)->where('status',1)->where('category_id',$category_id)->first();
        if((empty($categoryDetails) && $request->sid=='') || (  $request->sid!='' && empty($subCategoryDetails) )){
            Alert::error('Error', __(Helper::language('something_went_wrong')));
            return redirect('/');
        }

        $categories = Categories::with(['subcategory' => function ($query) use ($subcategory_id) {
            $query->where('status', 1);
            if (!empty($subcategory_id)) {
                $query->where('id', $subcategory_id);
            }
        }])
        ->where('id', $category_id)
        ->where('status', 1)
        ->get();

        
        $brand = array();
        $category = array();    
        $subcategory = array();

        $firstVariant = DB::table('product_variants as pv1')
        ->select('pv1.product_id', 'pv1.variant_price','pv1.variant_size','pv1.variant_uof')
        ->whereRaw('pv1.id = (
            SELECT pv2.id
            FROM product_variants pv2
            WHERE pv2.product_id = pv1.product_id
            ORDER BY pv2.id ASC
            LIMIT 1
        )');

        $products =  DB::table('product as p')
                    ->select('p.id as product_id','p.average_rating','p.product_name','p.product_name_fr','pi.image', 'pv.*', 'pv.final_price','p.category_id','p.subcategory_id','p.brand_id','first_variant.variant_price as product_price','first_variant.variant_size as product_size','first_variant.variant_uof as product_uof')
                    ->join('product_image as pi', 'p.id', '=', 'pi.product_id')
                    ->where('pi.status', '=', 1)
                    ->join('categories as c', 'p.category_id', '=', 'c.id')                    
                    ->where('c.status', '=', 1)
                    ->join('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id')
                    ->where('sc.status', '=', 1)
                    ->join('brand as b', 'p.brand_id', '=', 'b.id')
                    ->where('b.status', '=', 1)
                    ->join(
                        DB::raw('(SELECT *, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
                        function ($join) {
                            $join->on('p.id', '=', 'pv.product_id');
                        }
                    )
                    ->leftJoinSub($firstVariant, 'first_variant', function ($join) {
                        $join->on('first_variant.product_id', '=', 'p.id');
                    })
                    ->where('p.status', '=', 1)
                    ->groupBy('p.id');
                   // ->orderBy('pv.final_price', 'DESC')                   

                    if($category_id && $category_id!=null){
                        $products->where('c.id', '=', $category_id);
                    }
                    if($subcategory_id && $subcategory_id!=null){
                        $products->where('sc.id', '=', $subcategory_id);
                    }

                    $products  = $products->orderBy('p.id', 'DESC')->get();   

                    // getting Brands based on products only
                    $brandIds = $products->pluck('brand_id')->unique()->filter()->toArray();

                    $brandData = Brand::where('status', 1)
                        ->whereIn('id', $brandIds)
                        ->orderBy('title', 'asc')
                        ->get();

                    // Maximum and minimum price from the list
                    if (!$products->isEmpty()) {
                        // $max_price_value = $products->pluck('variant_price')->filter()->max();
                        $max_price_value = $products->pluck('product_price')->filter()->max();
                        $max_price_obj = new \stdClass();
                        $max_price_obj->max_price = number_format((float) $max_price_value, 2, '.', '');
                        $product_max_price=$max_price_obj;
                    }

                    $limit = 16;
                    $current_page_count = $request->current_page_count;
                    $total_product_count = count($products);
                    $productData = $products->skip($current_page_count)->take($limit);

                    foreach ($productData as $product) {
                        $today = Carbon::now()->toDateString();

                        // Bogo check
                        $bogo = Bogo::where('status', 1)
                            ->whereDate('start_date', '<=', $today)
                            ->whereDate('end_date', '>=', $today)
                            ->where(function ($query) use ($product) {
                                $query->where('product_id', $product->product_id);

                                if (!empty($product->subcategory_id)) {
                                    $query->orWhere('subcategory_id', $product->subcategory_id);
                                }

                                $query->orWhere(function ($q) use ($product) {
                                    $q->whereNull('subcategory_id')
                                    ->where('category_id', $product->category_id);
                                });

                                if (!empty($product->brand_id)) {
                                    $query->orWhere('brand_id', $product->brand_id);
                                }
                            })
                            ->first();

                        $product->bogo_status = $bogo ? true : false;

                        // offer Check
                        $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($product) {
                            $query->where('product_id', $product->product_id);

                            if (!empty($product->subcategory_id)) {
                                $query->orWhere('subcategory_id', $product->subcategory_id);
                            }

                            $query->orWhere(function ($q) use ($product) {
                                $q->whereNull('subcategory_id')
                                ->where('category_id', $product->category_id);
                            });

                            if (!empty($product->brand_id)) {
                                $query->orWhere('brand_id', $product->brand_id);
                            }
                        })
                        ->first();

                        if ($offer) {
                            $product->offer_status = true;
                            $product->discount_amount = $offer->dis_amount;
                            $product->offer_type = $offer->offer_type;
                        } else {
                            $product->offer_status = false;
                            $product->discount_amount = 0;
                            $product->offer_type = null;
                        }

                    }


                    $showing_product_count = 0;
                    if($total_product_count!=0 && $total_product_count >= $limit ){
                        $showing_product_count = $limit;
                    }elseif($total_product_count <= count($productData) ){
                        $showing_product_count = $total_product_count;
                    }
             
        return view("frontend.product.product-list",compact('categoryDetails','user_id','productData','brandData','categories','product_max_price','product_min_price','subCategoryDetails','total_product_count','showing_product_count','category_id','subcategory_id'));
    }

    public function filterproductlist(Request $request)
    {
        $keyword = $request->query('search'); 
        $keyword = trim($keyword);

        $user_id = $this->user_id;

        $categoryData = Categories::where('status', 1)->orderBy('id', 'ASC');

        $subcategoryData = SubCategories::withWhereHas('category', function ($query) {
            $query->where('status', 1);
        })->where('status', 1)->orderBy('id', 'ASC');

        $product_val =  new Product;
        $product_max_price = $product_val->getproductMaxPrice();
        $product_min_price = $product_val->getproductMinPrice();
        
        $firstVariant = DB::table('product_variants as pv1')
        ->select('pv1.product_id', 'pv1.variant_price','pv1.variant_size','pv1.variant_uof')
        ->whereRaw('pv1.id = (
            SELECT pv2.id
            FROM product_variants pv2
            WHERE pv2.product_id = pv1.product_id
            ORDER BY pv2.id ASC
            LIMIT 1
        )');

        $productDataFilter = DB::table('product as p')
        ->select(
            'p.id as product_id',
            'p.average_rating',
            'p.product_name',
            'p.product_name_fr',
            'pi.image',
            // DB::raw('COALESCE(NULLIF(pv.variant_discounted_price, ""), pv.variant_price) AS final_price'),
            'pv.final_price',
            'pv.*',
            'p.category_id',
            'p.subcategory_id',
            'p.brand_id',
            'first_variant.variant_price as product_price',
            'first_variant.variant_size as product_size',
            'first_variant.variant_uof as product_uof'
        )
        ->join('product_image as pi', function ($join) {
            $join->on('p.id', '=', 'pi.product_id')
                 ->where('pi.status', 1);
        })
        ->join('categories as c', function ($join) {
            $join->on('p.category_id', '=', 'c.id')
                 ->where('c.status', 1);
        })
        ->join('sub_categories as sc', function ($join) {
            $join->on('p.subcategory_id', '=', 'sc.id')
                 ->where('sc.status', 1);
        })
        ->join('brand as b', function ($join) {
            $join->on('p.brand_id', '=', 'b.id')
                 ->where('b.status', 1);
        })
        // ->join('product_variants as pv', function ($join) {
        //     $join->on('p.id', '=', 'pv.product_id')
        //          ->where('pv.status', 1);
        // })
        ->join(
            DB::raw('(SELECT *, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
            function ($join) {
                $join->on('p.id', '=', 'pv.product_id');
            }
        )
        ->leftJoinSub($firstVariant, 'first_variant', function ($join) {
                $join->on('first_variant.product_id', '=', 'p.id');
            })
        ->where('p.status', 1)
        ->groupBy('p.id');


        if(session::get('language')==2){
            $categoryData = $categoryData->where('title_fr', 'LIKE', '%'.$keyword.'%')->get();
            $subcategoryData = $subcategoryData->where('title_fr', 'LIKE', '%'.$keyword.'%')->get();
            $productDataFilter = $productDataFilter->where('p.product_name_fr', 'LIKE', '%'.$keyword.'%');
        }else{            
            $categoryData = $categoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
            $subcategoryData = $subcategoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
            $productDataFilter = $productDataFilter->where('p.product_name', 'LIKE', '%'.$keyword.'%');
        }

        $productDataFilter = $productDataFilter->orderBy('p.id', 'desc')->get();

        // pagination
        $limit = 16;
        $current_page_count = $request->current_page_count;
        $total_product_count = count($productDataFilter);
        $productData = $productDataFilter->skip($current_page_count)->take($limit);

         // getting Brands based on products only
        $brandIds = $productDataFilter->pluck('brand_id')->unique()->filter()->toArray();

        $brandData = Brand::where('status', 1)
            ->whereIn('id', $brandIds)
            ->orderBy('title', 'asc')
            ->get();

        // Similarly getting all the categories based on products only
        $categoryIds = $productDataFilter->pluck('category_id')->unique()->filter()->toArray();

        $categories = Categories::with(['subcategory'=>function ($query){
            $query->where('status',1);
        }])
        ->where('id', $categoryIds)
        ->where('status',1)->get();

        // Maximum and minimum price from the list
        if (!$productDataFilter->isEmpty()) {
            // $max_price_value = $productDataFilter->pluck('variant_price')->filter()->max();
             $max_price_value = $productDataFilter->pluck('product_price')->filter()->max();
            $max_price_obj = new \stdClass();
            $max_price_obj->max_price = number_format((float) $max_price_value, 2, '.', '');
            $product_max_price=$max_price_obj;
        }

        foreach ($productData as $product) {
                    $today = Carbon::now()->toDateString();

                    // Bogo Check
                    $bogo = Bogo::where('status', 1)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->where(function ($query) use ($product) {
                            $query->where('product_id', $product->product_id);

                            if (!empty($product->subcategory_id)) {
                                $query->orWhere('subcategory_id', $product->subcategory_id);
                            }

                            $query->orWhere(function ($q) use ($product) {
                                $q->whereNull('subcategory_id')
                                ->where('category_id', $product->category_id);
                            });

                            if (!empty($product->brand_id)) {
                                $query->orWhere('brand_id', $product->brand_id);
                            }
                        })
                        ->first();

                    $product->bogo_status = $bogo ? true : false;

                     // offer Check
                        $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($product) {
                            $query->where('product_id', $product->product_id);

                            if (!empty($product->subcategory_id)) {
                                $query->orWhere('subcategory_id', $product->subcategory_id);
                            }

                            $query->orWhere(function ($q) use ($product) {
                                $q->whereNull('subcategory_id')
                                ->where('category_id', $product->category_id);
                            });

                            if (!empty($product->brand_id)) {
                                $query->orWhere('brand_id', $product->brand_id);
                            }
                        })
                        ->first();

                    if ($offer) {
                        $product->offer_status = true;
                        $product->discount_amount = $offer->dis_amount;
                        $product->offer_type = $offer->offer_type;
                    } else {
                        $product->offer_status = false;
                        $product->discount_amount = 0;
                        $product->offer_type = null;
                    }
        }

        $showing_product_count = 0;
        if($total_product_count!=0 && $total_product_count >= $limit ){
            $showing_product_count = $limit;
        }elseif($total_product_count <= count($productData) ){
            $showing_product_count = $total_product_count;
        }

        return view("frontend.product.filtered-product-list",compact('categoryData','subcategoryData','productDataFilter','user_id','productData','brandData','product_max_price','product_min_price','total_product_count','showing_product_count','categories','keyword'));

    }

    public function loadfilterproductlist(Request $request)
    {
        $product_last_id_for_pagination = $request->product_last_id;
        $keyword = $request->keyword;
        $limit = 16;
        $brand_ids = null;
        $category_ids = null;
        $subcategory_ids = null;    

        if ($request->brand_ids) {
            $brand_ids = is_array($request->brand_ids)
                ? $request->brand_ids
                : explode(',', $request->brand_ids);
        }

        if ($request->category_ids) {
            $category_ids = is_array($request->category_ids)
                ? $request->category_ids
                : explode(',', $request->category_ids);
        }
        
        if ($request->subcategory_ids) {
            $subcategory_ids = is_array($request->subcategory_ids)
                ? $request->subcategory_ids
                : explode(',', $request->subcategory_ids);
        }

        // $minprice = ($request->min_price)?$request->min_price:'';
        // $maxprice = ($request->max_price)?$request->max_price:'';

        $minprice = $request->min_price !== null ? (float)$request->min_price : null;
        $maxprice = $request->max_price !== null ? (float)$request->max_price : null;

        $firstVariant = DB::table('product_variants as pv1')
        ->select('pv1.product_id', 'pv1.variant_price','pv1.variant_size','pv1.variant_uof')
        ->whereRaw('pv1.id = (
            SELECT pv2.id
            FROM product_variants pv2
            WHERE pv2.product_id = pv1.product_id
            ORDER BY pv2.id ASC
            LIMIT 1
        )');

        $productDataFilter = DB::table('product as p')
        ->select(
            'p.id as product_id',
            'p.average_rating',
            'p.product_name',
            'p.product_name_fr',
            'pi.image',
            'pv.final_price',
            'pv.*',
            'mvp.count',
            'p.category_id',
            'p.subcategory_id',
            'p.brand_id',
            'first_variant.variant_price as product_price',
            'first_variant.variant_size as product_size',
            'first_variant.variant_uof as product_uof'
        )
        ->join('product_image as pi', function ($join) {
            $join->on('p.id', '=', 'pi.product_id')
                 ->where('pi.status', 1);
        })
        ->join('categories as c', function ($join) {
            $join->on('p.category_id', '=', 'c.id')
                 ->where('c.status', 1);
        })
        ->join('sub_categories as sc', function ($join) {
            $join->on('p.subcategory_id', '=', 'sc.id')
                 ->where('sc.status', 1);
        })
        ->join('brand as b', function ($join) {
            $join->on('p.brand_id', '=', 'b.id')
                 ->where('b.status', 1);
        })
        ->join(
            DB::raw('(SELECT *, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
            function ($join) {
                $join->on('p.id', '=', 'pv.product_id');
            }
        )
        ->leftJoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
        ->leftJoinSub($firstVariant, 'first_variant', function ($join) {
                $join->on('first_variant.product_id', '=', 'p.id');
            })
        ->where('p.status', 1)
        ->groupBy('p.id');


        if($category_ids!=null && $subcategory_ids ==null){
            $productDataFilter->whereIn('sc.category_id', $category_ids);
        }
        else if($category_ids!=null && ($subcategory_ids && $subcategory_ids != null)){
            $productDataFilter->where(function ($query) use ($subcategory_ids,$category_ids){
                $query->whereIn('sc.id', $subcategory_ids)
                      ->orWhereIn('sc.category_id', $category_ids);
            });
        }
        else if($category_ids==null && ($subcategory_ids && $subcategory_ids != null)){
            $productDataFilter->whereIn('sc.id', $subcategory_ids);
        }

        if($brand_ids && $brand_ids != null){
            $productDataFilter->whereIn('b.id', $brand_ids);
        }

        // if(($minprice && $minprice != null) && ($maxprice && $maxprice != null)){                
        if (is_numeric($minprice) && is_numeric($maxprice)) { 
            // $productDataFilter->whereBetween('pv.final_price', [$minprice, $maxprice]);
             $productDataFilter->whereBetween('first_variant.variant_price', [$minprice, $maxprice]);
        }

        $sort_by = $request->sort_by;
        if($sort_by==1){
            $productDataFilter = $productDataFilter->orderBy('p.id', 'desc');
        }elseif($sort_by==2){
            // $productDataFilter =  $productDataFilter->orderBy('pv.final_price', 'desc');
              $productDataFilter =  $productDataFilter->orderBy('first_variant.variant_price', 'desc');
        }elseif($sort_by==3){
            // $productDataFilter = $productDataFilter->orderBy('pv.final_price', 'asc');
              $productDataFilter = $productDataFilter->orderBy('first_variant.variant_price', 'asc');
        }elseif($sort_by==4){
           $sorting_data =  ['mvp.count', 'desc'];
            // $productDataFilter->orderBy('mvp.count', 'desc');
        }else{
            $productDataFilter = $productDataFilter->orderBy('p.id', 'desc');
        }

        if(session::get('language')==2){
            $productDataFilter = $productDataFilter->where('p.product_name_fr', 'LIKE', '%'.$keyword.'%');
        }else{            
            $productDataFilter = $productDataFilter->where('p.product_name', 'LIKE', '%'.$keyword.'%');
        }

        $limit = 16;
        $current_page_count = $request->current_page_count;
        $productDataFilter  = $productDataFilter->get();  

        $total_product_count = count($productDataFilter);
        $productData = $productDataFilter->skip($current_page_count)->take($limit);

        foreach ($productData as $product) {
            $today = Carbon::now()->toDateString();

            // Bogo Check
            $bogo = Bogo::where('status', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->where(function ($query) use ($product) {
                    $query->where('product_id', $product->product_id);

                    if (!empty($product->subcategory_id)) {
                        $query->orWhere('subcategory_id', $product->subcategory_id);
                    }

                    $query->orWhere(function ($q) use ($product) {
                        $q->whereNull('subcategory_id')
                        ->where('category_id', $product->category_id);
                    });

                    if (!empty($product->brand_id)) {
                        $query->orWhere('brand_id', $product->brand_id);
                    }
                })
                ->first();

            $product->bogo_status = $bogo ? true : false;

             // offer Check
                        $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($product) {
                            $query->where('product_id', $product->product_id);

                            if (!empty($product->subcategory_id)) {
                                $query->orWhere('subcategory_id', $product->subcategory_id);
                            }

                            $query->orWhere(function ($q) use ($product) {
                                $q->whereNull('subcategory_id')
                                ->where('category_id', $product->category_id);
                            });

                            if (!empty($product->brand_id)) {
                                $query->orWhere('brand_id', $product->brand_id);
                            }
                        })
                        ->first();

                    if ($offer) {
                        $product->offer_status = true;
                        $product->discount_amount = $offer->dis_amount;
                        $product->offer_type = $offer->offer_type;
                    } else {
                        $product->offer_status = false;
                        $product->discount_amount = 0;
                        $product->offer_type = null;
                    }
        }

        $showing_product_count = 0;
        if($total_product_count!=0 && $total_product_count >= $limit ){
            $showing_product_count = $limit;
        }elseif($total_product_count <= count($productData) ){
            $showing_product_count = $total_product_count;
        }

        $data = ['total_product_count'=>$total_product_count,'showing_product_count'=>$showing_product_count,'productData'=>$productData];

        return view("frontend.product.ajax_filter_product",$data );

    }


    public function filterbrandlist(Request $request)
    {
        $user_id = $this->user_id;
        $brand_id=base64_decode($request->id);

        $categoryData = Categories::where('status', 1)->orderBy('id', 'ASC');

        $subcategoryData = SubCategories::withWhereHas('category', function ($query) {
            $query->where('status', 1);
        })->where('status', 1)->orderBy('id', 'ASC');

        $product_val =  new Product;
        $product_max_price = $product_val->getproductMaxPrice();
        $product_min_price = $product_val->getproductMinPrice();

        $myBrand = Brand::where('status', 1)->where('id', $brand_id)->first();

        if(session::get('language')==2){
            $keyword = $myBrand ? $myBrand->title_fr : ''; 
        }
        else
        {
            $keyword = $myBrand ? $myBrand->title : ''; 
        }

         $firstVariant = DB::table('product_variants as pv1')
        ->select('pv1.product_id', 'pv1.variant_price','pv1.variant_size','pv1.variant_uof')
        ->whereRaw('pv1.id = (
            SELECT pv2.id
            FROM product_variants pv2
            WHERE pv2.product_id = pv1.product_id
            ORDER BY pv2.id ASC
            LIMIT 1
        )');

        $productDataFilter = DB::table('product as p')
        ->select(
            'p.id as product_id',
            'p.average_rating',
            'p.product_name',
            'p.product_name_fr',
            'pi.image',
            'pv.final_price',
            'pv.*',
            'p.category_id',
            'p.subcategory_id',
            'p.brand_id',
            'first_variant.variant_price as product_price',
            'first_variant.variant_size as product_size',
            'first_variant.variant_uof as product_uof'
        )
        ->join('product_image as pi', function ($join) {
            $join->on('p.id', '=', 'pi.product_id')
                 ->where('pi.status', 1);
        })
        ->join('categories as c', function ($join) {
            $join->on('p.category_id', '=', 'c.id')
                 ->where('c.status', 1);
        })
        ->join('sub_categories as sc', function ($join) {
            $join->on('p.subcategory_id', '=', 'sc.id')
                 ->where('sc.status', 1);
        })
        ->join('brand as b', function ($join) {
            $join->on('p.brand_id', '=', 'b.id')
                 ->where('b.status', 1);
        })
        ->join(
            DB::raw('(SELECT *, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
            function ($join) {
                $join->on('p.id', '=', 'pv.product_id');
            }
        )
        ->leftJoinSub($firstVariant, 'first_variant', function ($join) {
            $join->on('first_variant.product_id', '=', 'p.id');
        })
        ->where('p.status', 1)
        ->where('p.brand_id', $brand_id) 
        ->groupBy('p.id')
        ->orderBy('p.id', 'desc')
        ->get();


        $limit = 16;
        $total_product_count = count($productDataFilter);
        $productData = $productDataFilter->take($limit);

        // getting Brands based on products only
        $brandIds = $productDataFilter->pluck('brand_id')->unique()->filter()->toArray();

        $brandData = Brand::where('status', 1)
            ->whereIn('id', $brandIds)
            ->orderBy('title', 'asc')
            ->get();

        // Similarly getting all the categories based on products only
        $categoryIds = $productDataFilter->pluck('category_id')->unique()->filter()->toArray();

        $categories = Categories::with(['subcategory'=>function ($query){
            $query->where('status',1);
        }])
        ->where('id', $categoryIds)
        ->where('status',1)->get();

        // Maximum and minimum price from the list
        if (!$productDataFilter->isEmpty()) {
            // $max_price_value = $productDataFilter->pluck('variant_price')->filter()->max();
            $max_price_value = $productDataFilter->pluck('product_price')->filter()->max();
            $max_price_obj = new \stdClass();
            $max_price_obj->max_price = number_format((float) $max_price_value, 2, '.', '');
            $product_max_price=$max_price_obj;
        }

        foreach ($productData as $product) {
            $today = Carbon::now()->toDateString();

            // bogo Check
            $bogo = Bogo::where('status', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->where(function ($query) use ($product) {
                    $query->where('product_id', $product->product_id);

                    if (!empty($product->subcategory_id)) {
                        $query->orWhere('subcategory_id', $product->subcategory_id);
                    }

                    $query->orWhere(function ($q) use ($product) {
                        $q->whereNull('subcategory_id')
                        ->where('category_id', $product->category_id);
                    });

                    if (!empty($product->brand_id)) {
                        $query->orWhere('brand_id', $product->brand_id);
                    }
                })
                ->first();

            $product->bogo_status = $bogo ? true : false;


             // offer Check
                        $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($product) {
                            $query->where('product_id', $product->product_id);

                            if (!empty($product->subcategory_id)) {
                                $query->orWhere('subcategory_id', $product->subcategory_id);
                            }

                            $query->orWhere(function ($q) use ($product) {
                                $q->whereNull('subcategory_id')
                                ->where('category_id', $product->category_id);
                            });

                            if (!empty($product->brand_id)) {
                                $query->orWhere('brand_id', $product->brand_id);
                            }
                        })
                        ->first();

                    if ($offer) {
                        $product->offer_status = true;
                        $product->discount_amount = $offer->dis_amount;
                        $product->offer_type = $offer->offer_type;
                    } else {
                        $product->offer_status = false;
                        $product->discount_amount = 0;
                        $product->offer_type = null;
                    }
        }

        $showing_product_count = 0;
        if($total_product_count!=0 && $total_product_count >= $limit ){
            $showing_product_count = $limit;
        }elseif($total_product_count <= $limit ){
            $showing_product_count = $total_product_count;
        }

        return view("frontend.product.brand-list",compact('categoryData','subcategoryData','productDataFilter','user_id','productData','brandData','product_max_price','product_min_price','total_product_count','showing_product_count','categories','keyword','brand_id'));

        
    }

    public function loadbrandlist(Request $request)
    {
        $product_last_id_for_pagination = $request->product_last_id;
        $limit = 16;
        $brand_ids = null;
        $category_ids = null;
        $subcategory_ids = null; 
        $keyword = $request->keyword;
        $brand_id=$request->myBrand;    

        if ($request->brand_ids) {
            $brand_ids = is_array($request->brand_ids)
                ? $request->brand_ids
                : explode(',', $request->brand_ids);
        }

        if ($request->category_ids) {
            $category_ids = is_array($request->category_ids)
                ? $request->category_ids
                : explode(',', $request->category_ids);
        }
        
        if ($request->subcategory_ids) {
            $subcategory_ids = is_array($request->subcategory_ids)
                ? $request->subcategory_ids
                : explode(',', $request->subcategory_ids);
        }

        // $minprice = ($request->min_price)?$request->min_price:'';
        // $maxprice = ($request->max_price)?$request->max_price:'';

        $minprice = $request->min_price !== null ? (float)$request->min_price : null;
        $maxprice = $request->max_price !== null ? (float)$request->max_price : null;

        $firstVariant = DB::table('product_variants as pv1')
        ->select('pv1.product_id', 'pv1.variant_price','pv1.variant_size','pv1.variant_uof')
        ->whereRaw('pv1.id = (
            SELECT pv2.id
            FROM product_variants pv2
            WHERE pv2.product_id = pv1.product_id
            ORDER BY pv2.id ASC
            LIMIT 1
        )');

        $productDataFilter = DB::table('product as p')
        ->select(
            'p.id as product_id',
            'p.average_rating',
            'p.product_name',
            'p.product_name_fr',
            'pi.image',
            'pv.final_price',
            'pv.*',
            'mvp.count',
            'p.category_id',
            'p.subcategory_id',
            'p.brand_id',
            'first_variant.variant_price as product_price',
            'first_variant.variant_size as product_size',
            'first_variant.variant_uof as product_uof'
        )
        ->join('product_image as pi', function ($join) {
            $join->on('p.id', '=', 'pi.product_id')
                 ->where('pi.status', 1);
        })
        ->join('categories as c', function ($join) {
            $join->on('p.category_id', '=', 'c.id')
                 ->where('c.status', 1);
        })
        ->join('sub_categories as sc', function ($join) {
            $join->on('p.subcategory_id', '=', 'sc.id')
                 ->where('sc.status', 1);
        })
        ->join('brand as b', function ($join) {
            $join->on('p.brand_id', '=', 'b.id')
                 ->where('b.status', 1);
        })
        ->join(
            DB::raw('(SELECT *, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
            function ($join) {
                $join->on('p.id', '=', 'pv.product_id');
            }
        )
        ->leftJoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
        ->leftJoinSub($firstVariant, 'first_variant', function ($join) {
                $join->on('first_variant.product_id', '=', 'p.id');
            })
        ->where('p.status', 1)
        ->where('p.brand_id', $brand_id) 
        ->groupBy('p.id');
        

        if($category_ids!=null && $subcategory_ids ==null){
            $productDataFilter->whereIn('sc.category_id', $category_ids);
        }
        else if($category_ids!=null && ($subcategory_ids && $subcategory_ids != null)){
            $productDataFilter->where(function ($query) use ($subcategory_ids,$category_ids){
                $query->whereIn('sc.id', $subcategory_ids)
                      ->orWhereIn('sc.category_id', $category_ids);
            });
        }
        else if($category_ids==null && ($subcategory_ids && $subcategory_ids != null)){
            $productDataFilter->whereIn('sc.id', $subcategory_ids);
        }

        if($brand_ids && $brand_ids != null){
            $productDataFilter->whereIn('b.id', $brand_ids);
        }

        // if(($minprice && $minprice != null) && ($maxprice && $maxprice != null)){                
        if (is_numeric($minprice) && is_numeric($maxprice)) { 
            // $productDataFilter->whereBetween('pv.final_price', [$minprice, $maxprice]);
            $productDataFilter->whereBetween('first_variant.variant_price', [$minprice, $maxprice]);
        }

        $sort_by = $request->sort_by;
        if($sort_by==1){
            $productDataFilter = $productDataFilter->orderBy('p.id', 'desc');
        }elseif($sort_by==2){
            // $productDataFilter =  $productDataFilter->orderBy('pv.final_price', 'desc');
            $productDataFilter =  $productDataFilter->orderBy('first_variant.variant_price', 'desc');
        }elseif($sort_by==3){
            // $productDataFilter = $productDataFilter->orderBy('pv.final_price', 'asc');
            $productDataFilter = $productDataFilter->orderBy('first_variant.variant_price', 'asc');
        }elseif($sort_by==4){
           $sorting_data =  ['mvp.count', 'desc'];
            // $productDataFilter->orderBy('mvp.count', 'desc');
        }else{
            $productDataFilter = $productDataFilter->orderBy('p.id', 'desc');
        }

        $limit = 16;
        $current_page_count = $request->current_page_count;

        $productDataFilter  = $productDataFilter->get();  

        $total_product_count = count($productDataFilter);
        $productData = $productDataFilter->skip($current_page_count)->take($limit);

        foreach ($productData as $product) {
            $today = Carbon::now()->toDateString();

            // Bogo Check
            $bogo = Bogo::where('status', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->where(function ($query) use ($product) {
                    $query->where('product_id', $product->product_id);

                    if (!empty($product->subcategory_id)) {
                        $query->orWhere('subcategory_id', $product->subcategory_id);
                    }

                    $query->orWhere(function ($q) use ($product) {
                        $q->whereNull('subcategory_id')
                        ->where('category_id', $product->category_id);
                    });

                    if (!empty($product->brand_id)) {
                        $query->orWhere('brand_id', $product->brand_id);
                    }
                })
                ->first();

            $product->bogo_status = $bogo ? true : false;

             // offer Check
                        $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($product) {
                            $query->where('product_id', $product->product_id);

                            if (!empty($product->subcategory_id)) {
                                $query->orWhere('subcategory_id', $product->subcategory_id);
                            }

                            $query->orWhere(function ($q) use ($product) {
                                $q->whereNull('subcategory_id')
                                ->where('category_id', $product->category_id);
                            });

                            if (!empty($product->brand_id)) {
                                $query->orWhere('brand_id', $product->brand_id);
                            }
                        })
                        ->first();

                    if ($offer) {
                        $product->offer_status = true;
                        $product->discount_amount = $offer->dis_amount;
                        $product->offer_type = $offer->offer_type;
                    } else {
                        $product->offer_status = false;
                        $product->discount_amount = 0;
                        $product->offer_type = null;
                    }
        }

        $showing_product_count = 0;
        if($total_product_count!=0 && $total_product_count >= $limit ){
            $showing_product_count = $limit;
        }elseif($total_product_count <= count($productData) ){
            $showing_product_count = $total_product_count;
        }

        $data = ['total_product_count'=>$total_product_count,'showing_product_count'=>$showing_product_count,'productData'=>$productData];

        return view("frontend.product.ajax_brand",$data );

    }

    public function filterData(Request $request)
    {
        $product_last_id_for_pagination = $request->product_last_id;
        $limit = 16;
        $brand_ids = null;
        $category_ids = null;
        $subcategory_ids = null;

        $category_id=$request->category_id;
        $subcategory_id=$request->subcategory_id;

        // if($request->brand_ids!=""){
        //     $brand_ids = explode(',',$request->brand_ids);
        // }  
        if ($request->brand_ids) {
            $brand_ids = is_array($request->brand_ids)
                ? $request->brand_ids
                : explode(',', $request->brand_ids);
        }
        

        // if($request->category_ids){
        //     $category_ids = explode(',',$request->category_ids);
        // }

        if ($request->category_ids) {
            $category_ids = is_array($request->category_ids)
                ? $request->category_ids
                : explode(',', $request->category_ids);
        }
        


        // if($request->subcategory_ids){
        //     $subcategory_ids = explode(',',$request->subcategory_ids);
        // }

        if ($request->subcategory_ids) {
            $subcategory_ids = is_array($request->subcategory_ids)
                ? $request->subcategory_ids
                : explode(',', $request->subcategory_ids);
        }
        
        $subcategory = array();
        // $minprice = ($request->min_price)?$request->min_price:'';
        // $maxprice = ($request->max_price)?$request->max_price:'';

        $minprice = $request->min_price !== null ? (float)$request->min_price : null;
        $maxprice = $request->max_price !== null ? (float)$request->max_price : null;

        $firstVariant = DB::table('product_variants as pv1')
        ->select('pv1.product_id', 'pv1.variant_price','pv1.variant_size','pv1.variant_uof')
        ->whereRaw('pv1.id = (
            SELECT pv2.id
            FROM product_variants pv2
            WHERE pv2.product_id = pv1.product_id
            ORDER BY pv2.id ASC
            LIMIT 1
        )');
        
        $products =  DB::table('product as p')
                    ->select('p.id as product_id','p.average_rating','p.product_name','p.product_name_fr','pi.image', 'pv.*', 'pv.final_price','mvp.count','p.category_id','p.subcategory_id','p.brand_id','first_variant.variant_price as product_price','first_variant.variant_size as product_size','first_variant.variant_uof as product_uof')
                    ->join('product_image as pi', 'p.id', '=', 'pi.product_id')
                    ->where('pi.status', '=', 1)
                    ->join('categories as c', 'p.category_id', '=', 'c.id')                    
                        ->where('c.status', '=', 1)
                    ->join('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id')
                        ->where('sc.status', '=', 1)
                    ->join('brand as b', 'p.brand_id', '=', 'b.id')
                        ->where('b.status', '=', 1)
                    ->join(
                        DB::raw('(SELECT *, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
                        function ($join) {
                            $join->on('p.id', '=', 'pv.product_id');
                        }
                    )
                    ->leftjoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
                    ->leftJoinSub($firstVariant, 'first_variant', function ($join) {
                        $join->on('first_variant.product_id', '=', 'p.id');
                    })
                    ->where('p.status', '=', 1)
                    ->groupBy('p.id');

                    if($category_ids!=null && $subcategory_ids ==null){
                        $products->whereIn('sc.category_id', $category_ids);
                    }else if($category_ids!=null && ($subcategory_ids && $subcategory_ids != null)){
                        $products->where(function ($query) use ($subcategory_ids,$category_ids){
                            $query->whereIn('sc.id', $subcategory_ids)
                                  ->orWhereIn('sc.category_id', $category_ids);
                        });
                    }else if($category_ids==null && ($subcategory_ids && $subcategory_ids != null)){
                        $products->whereIn('sc.id', $subcategory_ids);
                    }

                    if($brand_ids && $brand_ids != null){
                        $products->whereIn('b.id', $brand_ids);
                    }

                    // if(($minprice && $minprice != null) && ($maxprice && $maxprice != null)){                
                    if (is_numeric($minprice) && is_numeric($maxprice)) {                
                        // $products->whereBetween('pv.final_price', [$minprice, $maxprice]);
                         $products->whereBetween('first_variant.variant_price', [$minprice, $maxprice]);
                    }

                    if($category_id && $category_id!=null){
                        $products->where('c.id', '=', $category_id);
                    }

                    if($subcategory_id && $subcategory_id!=null){
                        $products->where('sc.id', '=', $subcategory_id);
                    }

                    $sort_by = $request->sort_by;
                    if($sort_by==1){
                        $products = $products->orderBy('p.id', 'desc');
                    }elseif($sort_by==2){
                        // $products =  $products->orderBy('pv.final_price', 'desc');
                         $products =  $products->orderBy('first_variant.variant_price', 'desc');
                    }elseif($sort_by==3){
                        // $products = $products->orderBy('pv.final_price', 'asc');
                        $products = $products->orderBy('first_variant.variant_price', 'asc');
                    }elseif($sort_by==4){
                       $sorting_data =  ['mvp.count', 'desc'];
                        // $products->orderBy('mvp.count', 'desc');
                    }else{
                        $products = $products->orderBy('p.id', 'desc');
                    }

                    $limit = 16;
                    $current_page_count = $request->current_page_count;
                    $products  = $products->get();  
                    $total_product_count = count($products);
                    $productData = $products->skip($current_page_count)->take($limit);

                    foreach ($productData as $product) {
                        $today = Carbon::now()->toDateString();

                        // Bogo Check
                        $bogo = Bogo::where('status', 1)
                            ->whereDate('start_date', '<=', $today)
                            ->whereDate('end_date', '>=', $today)
                            ->where(function ($query) use ($product) {
                                $query->where('product_id', $product->product_id);

                                if (!empty($product->subcategory_id)) {
                                    $query->orWhere('subcategory_id', $product->subcategory_id);
                                }

                                $query->orWhere(function ($q) use ($product) {
                                    $q->whereNull('subcategory_id')
                                    ->where('category_id', $product->category_id);
                                });

                                if (!empty($product->brand_id)) {
                                    $query->orWhere('brand_id', $product->brand_id);
                                }
                            })
                            ->first();

                        $product->bogo_status = $bogo ? true : false;

                         // offer Check
                        $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($product) {
                            $query->where('product_id', $product->product_id);

                            if (!empty($product->subcategory_id)) {
                                $query->orWhere('subcategory_id', $product->subcategory_id);
                            }

                            $query->orWhere(function ($q) use ($product) {
                                $q->whereNull('subcategory_id')
                                ->where('category_id', $product->category_id);
                            });

                            if (!empty($product->brand_id)) {
                                $query->orWhere('brand_id', $product->brand_id);
                            }
                        })
                        ->first();

                        if ($offer) {
                            $product->offer_status = true;
                            $product->discount_amount = $offer->dis_amount;
                            $product->offer_type = $offer->offer_type;
                        } else {
                            $product->offer_status = false;
                            $product->discount_amount = 0;
                            $product->offer_type = null;
                        }
                    }
        
                    $showing_product_count = 0;
                    if($total_product_count!=0 && $total_product_count >= $limit ){
                        $showing_product_count = $limit;
                    }elseif($total_product_count <= count($productData) ){
                        $showing_product_count = $total_product_count;
                    }
                
                $data = ['total_product_count'=>$total_product_count,'showing_product_count'=>$showing_product_count,'productData'=>$productData];

                return view("frontend.product.ajax_product",$data );
    }
 


    public function filterDataold(Request $request)
    {
        $product_last_id_for_pagination = $request->product_last_id;
        $limit = 16;
        $brand_ids = array();
        $category_ids = null;
        $subcategory_ids = null;
        if($request->brand_ids){
            $brand_ids = explode(',',$request->brand_ids);
        }      
              
        // if($request->category_ids) {
        //     // Check if category_ids is already an array
        //     if (is_array($request->category_ids)) {
        //         $category_ids = $request->category_ids; // Use it directly as it is already an array
        //     } else {
        //         $category_ids = explode(',', $request->category_ids); // If it's a string, split by comma
        //     }
        // }
        
        
        if($request->category_ids){
            $category_ids = explode(',',$request->category_ids);
        }
        if($request->subcategory_ids){
            $subcategory_ids = explode(',',$request->subcategory_ids);
        }

        $subcategory = array();
        $minprice = ($request->min_price)?$request->min_price:'';
        $maxprice = ($request->max_price)?$request->max_price:'';

        $products = Product::withWhereHas('get_product_images', function ($query){
            $query->where('status', 1);
        })->withWhereHas('get_category', function ($query)  use ($category_ids) {
            $query->where('status', 1);
            // $query->when(($category_ids && $category_ids != null), function ($q) use ($category_ids) {
            //     return $q->whereIn('id', $category_ids);
            // });
        })->withWhereHas('get_subcategory', function ($query) use ($subcategory_ids, $category_ids){
            $query->where('status', 1);

            $query->when(($category_ids!=null && $subcategory_ids ==null), function ($q) use ($subcategory_ids, $category_ids) {
                return $q->whereIn('category_id', $category_ids);
            });
            
            $query->when(($category_ids!=null && ($subcategory_ids && $subcategory_ids != null)), function ($q) use ($subcategory_ids, $category_ids) {
                return $q->whereIn('id', $subcategory_ids)->orwhereIn('category_id', $category_ids);
            });

            $query->when(($category_ids==null && ($subcategory_ids && $subcategory_ids != null)), function ($q) use ($subcategory_ids, $category_ids) {
                return $q->whereIn('id', $subcategory_ids);
            });

        })->withWhereHas('get_brand_details', function ($query) use ($brand_ids) {
            $query->where('status', 1);
            $query->when(($brand_ids && $brand_ids != null), function ($q) use ($brand_ids) {
                return $q->whereIn('id', $brand_ids);
            });
        })->withWhereHas('variant_product_price', function ($query) use ($minprice,$maxprice) {
            $query->when(($minprice && $minprice != null) && ($maxprice && $maxprice != null), function ($q) use ($minprice,$maxprice) {                
                return $q->whereBetween('variant_price', [$minprice, $maxprice]);
            });
        })->with('mostViewProduct',function ($query) {
           $query->where('status', 1);
        })->where('status',1);
        //dd($products->get());
        $total_product_count = count($products->get()); 
        
        $showing_product_count = 0;
        if($product_last_id_for_pagination){  
            $productData = $products->orderBy('product.id', 'desc')->where('product.id', '<', $product_last_id_for_pagination)->limit($limit)->get();
        }else{
            $productData = $products->orderBy('product.id', 'desc')->limit($limit)->get();
        }        
        if($total_product_count!=0 && $total_product_count >= $limit ){
            $showing_product_count = $limit;
        }elseif($total_product_count <= count($productData) ){
            $showing_product_count = $total_product_count;
        }
       
        $data = ['total_product_count'=>$total_product_count,'showing_product_count'=>$showing_product_count,'productData'=>$productData];

        return view("frontend.product.ajax_product",$data );
    }


    public function sortByList(Request $request){       
        
        $productIds = explode(',',$request->productIds);   
        $product_ids = [];
        foreach($productIds as $value){
            $product_ids[] = base64_decode($value);
        } 
        $products = Product::withWhereHas('get_product_images', function ($query){
            $query->where('status', 1);
        })->withWhereHas('get_category', function ($query){
            $query->where('status', 1);
        })->withWhereHas('get_subcategory', function ($query){
            $query->where('status', 1);
        })->withWhereHas('get_brand_details', function ($query){
            $query->where('status', 1);
        })
        ->withWhereHas('variant_product_price', function ($query) {
            return $query->select('*', DB::raw("COALESCE(NULLIF (product_variants.variant_discounted_price,''),product_variants.variant_price ) as final_price"))   ;
        })
        ->with('mostViewProduct',function ($query) {
            $query->where('status', 1);
        })
        ->where('status',1)->whereIn('product.id',$product_ids)->get();
   //dd($products);
        $sort_by = $request->sort_by;
        if($sort_by==1){
            $sorting_data =  ['created_at', 'desc'];
        }elseif($sort_by==2){
            $sorting_data = ['variant_product_price.final_price', 'desc'];
        }elseif($sort_by==3){
            $sorting_data = ['variant_product_price.final_price', 'asc'];
        }elseif($sort_by==4){
            $sorting_data =  ['mostViewProduct.count', 'desc'];
        }else{
            $sorting_data = ['created_at', 'asc'];
        }
        $productData = $products->sortBy([
            $sorting_data
        ]);

        // //dd(count($products));
        $productData = $productData->values();
        // // dd($productData);
        $showing_product_count = count($productData);
        ///sort by for not change last_id 
        $product_last_id = $request->last_id;
        return view("frontend.product.ajax_product",compact('productData','product_last_id'));
    }

    public function productDetails(Request $request, $id)
    {
        // die();
       //dd(Session::get('cart_info'));
        $id = base64_decode($id); 
        $user_id = $this->user_id;

        $mostViewProduct = MostViewProduct::where('product_id',$id)->first();
        if(!empty($mostViewProduct)){
            $mostViewProduct->count = (int) $mostViewProduct->count + 1;
            $mostViewProduct->save();
        }else{
            $mostViewProduct = new MostViewProduct();
            $mostViewProduct->product_id = $id;
            $mostViewProduct->count = 1;
            $mostViewProduct->status = 1;
            $mostViewProduct->save();
        }
        /*-- Add product ids in the session for user recent view product---*/ 
        $recent_view = Session::get('recent_view_product_ids');  
        if($recent_view ==""){           
            Session::put('recent_view_product_ids',array($id));
        }else{          
           if(!in_array($id,$recent_view)){    
                $new = array($id);       
                $add_new_value = array_merge($recent_view, $new);                            
                Session::put('recent_view_product_ids',$add_new_value);
           }
        }
        //dd(Session::get('recent_view_product_ids')); 
        $settings = Setting::find(1);
        $product_info = Product::activeProductsBasedOnRelations()->where('product.id',$id)->first();
        if(empty($product_info)){
            //Alert::error('Error', __(Helper::language('something_went_wrong')));
            return redirect('/');
        }
        $product_subcategoryid = $product_info->subcategory_id;
        $relatedProduct = Product::activeProductsBasedOnRelations()->where('product.subcategory_id',$product_subcategoryid)->where('product.id',"!=",$id)->limit(6)->get();
       
        $subCatgyIds = MostViewProduct::getMostViewProductSubcategoryIds();
      
        $recommendedProduct = Product::activeProductsBasedOnRelations()->whereIn('product.subcategory_id',$subCatgyIds)->where('product.id',"!=",$id)->limit(6)->get();
     

        $productRating = Product::productUserRating($id);
        //Cart data
        $cartData = Cart::where('user_id', $user_id)->where('product_id', $id)->where('status', 1)->first();

        $today = Carbon::now()->toDateString();

        // Related Product Bogo
        if($relatedProduct)
        {
            foreach ($relatedProduct as $related) {
                    $bogo = Bogo::where('status', 1)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->where(function ($query) use ($related) {
                            $query->where('product_id', $related->id)
                                ->orWhere(function ($q) use ($related) {
                                    $q->whereNotNull('subcategory_id')
                                        ->where('subcategory_id', $related->subcategory_id);
                                })
                                ->orWhere(function ($q) use ($related) {
                                    $q->whereNull('subcategory_id')
                                        ->where('category_id', $related->category_id);
                                });

                            if (!empty($related->brand_id)) {
                                $query->orWhere('brand_id', $related->brand_id);
                            }
                        })
                        ->first();

                    $related->bogo_status = $bogo ? true : false; 


                    // offer check
                     $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($related) {
                    $query->where('product_id', $related->id)
                        ->orWhere(function ($q) use ($related) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $related->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($related) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $related->category_id);
                        });

                    if (!empty($related->brand_id)) {
                        $query->orWhere('brand_id', $related->brand_id);
                    }
                })
                ->first();

                if ($offer) {
                    $related->offer_status = true;
                    $related->discount_amount = $offer->dis_amount;
                    $related->offer_type = $offer->offer_type;
                } else {
                    $related->offer_status = false;
                    $related->discount_amount = 0;
                    $related->offer_type = null;
                }
            }
        }

        // Recommended Product Bogo
        if($recommendedProduct)
        {
            foreach ($recommendedProduct as $recommend) {
                $bogo = Bogo::where('status', 1)
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->where(function ($query) use ($recommend) {
                        $query->where('product_id', $recommend->id)
                            ->orWhere(function ($q) use ($recommend) {
                                $q->whereNotNull('subcategory_id')
                                    ->where('subcategory_id', $recommend->subcategory_id);
                            })
                            ->orWhere(function ($q) use ($recommend) {
                                $q->whereNull('subcategory_id')
                                    ->where('category_id', $recommend->category_id);
                            });

                        if (!empty($recommend->brand_id)) {
                            $query->orWhere('brand_id', $recommend->brand_id);
                        }
                    })
                    ->first();

                $recommend->bogo_status = $bogo ? true : false; 

                // offer check
                     $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($recommend) {
                    $query->where('product_id', $recommend->id)
                        ->orWhere(function ($q) use ($recommend) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $recommend->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($recommend) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $recommend->category_id);
                        });

                    if (!empty($recommend->brand_id)) {
                        $query->orWhere('brand_id', $recommend->brand_id);
                    }
                })
                ->first();

                if ($offer) {
                    $recommend->offer_status = true;
                    $recommend->discount_amount = $offer->dis_amount;
                    $recommend->offer_type = $offer->offer_type;
                } else {
                    $recommend->offer_status = false;
                    $recommend->discount_amount = 0;
                    $recommend->offer_type = null;
                }
            }
        }

        // Bogo Status Check
        $bogoStatus=false;

        $bogo = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where(function ($query) use ($product_info) {
                $query->where('product_id', $product_info->id)
                    ->orWhere(function ($q) use ($product_info) {
                        $q->whereNotNull('subcategory_id')
                        ->where('subcategory_id', $product_info->subcategory_id);
                    })
                    ->orWhere(function ($q) use ($product_info) {
                        $q->whereNull('subcategory_id')
                        ->where('category_id', $product_info->category_id);
                    });

                if (!empty($product_info->brand_id)) {
                    $query->orWhere('brand_id', $product_info->brand_id);
                }
            })
            ->first();


        if ($bogo) {
            $bogoStatus=true;
        } 


        // offer status check  
        $offerDetails=[];
        $offer = Offers::where('status', 1)
        ->whereDate('expiry_date', '>=', $today)
        ->where(function ($query) use ($product_info) {
            $query->where('product_id', $product_info->id)
                ->orWhere('category_id', $product_info->category_id)
                ->orWhere('subcategory_id', $product_info->subcategory_id)
                ->orWhere('brand_id', $product_info->brand_id);
        })
        ->first(); 

        if ($offer) {
            $offerDetails = [
                'offer_type' => $offer->offer_type,
                'discount_amount' => $offer->dis_amount,
                'offer_status'=> $offer ? true : false
            ];
        } 

        return view("frontend.product.product-detail",compact('product_info','settings','relatedProduct','productRating','recommendedProduct','id','cartData','bogoStatus','offerDetails'));
    }
    /*--This function is using for fetching variant price. 
    when user click on pack size on product details page.---*/
    public function productVariantPrice(Request $request){
        $user_id = $this->user_id;
        $varinat_id = base64_decode($request->variantId);
        $varinat_info = ProductVariants::where('id',$varinat_id)->first();
        $get_cart_array = Session::get('cart_info');
        $product_id = $varinat_info->product_id;
        $is_in_cart = false;
        if(isset($get_cart_array)){           
            if(array_key_exists($product_id,$get_cart_array)){
                foreach($get_cart_array as $key => $variant_array){   
                    if($key==$product_id && array_key_exists($varinat_id,$variant_array)){                       
                        $is_in_cart = true;
                    }
                }
            }
            $user_cart_count = Helper::getCartQuantity($varinat_id);
        }else{            
            if($user_id!=""){                
                $cartData = Cart::where('user_id', $user_id)->where('product_id', $product_id)->where('product_variant_id',$varinat_id)->where('status', 1)->first();
                if($cartData){
                    $is_in_cart = true;
                    $user_cart_count = $cartData->quantity;
                }
            }
           
        }
        $instock = false;
        if($varinat_info->available_qty > 0){
            $instock = true;
        }

        // if($varinat_info->variant_discounted_price=='' || $varinat_info->variant_discounted_price==0)
        // {
        //     $varinat_disocunt_price = $varinat_info->variant_price ;
        // }else{
        //     $varinat_disocunt_price = $varinat_info->variant_discounted_price ;
        // }

        //   return response()->json(['orignal_price' => $varinat_info->variant_price, 'discounted_price' => $varinat_disocunt_price,'product_stock'=>$instock,'is_in_cart'=>$is_in_cart]);


        // Updated for offer
        $original_price = $varinat_info->variant_price;
        $discounted_price = $original_price; 

        $today = now()->toDateString();
        $offer = Offers::where('status', 1)
        ->whereDate('expiry_date', '>=', $today)
        ->first();


         if ($offer) {
                $is_offer_applicable = false;
                $product_info = Product::activeProductsBasedOnRelations()->where('product.id',$product_id)->first();

                // Match the offer to the product
                if (!empty($offer->product_id) && $offer->product_id == $product_id) {
                    $is_offer_applicable = true;
                } elseif (!empty($offer->brand_id) && $offer->brand_id == $product_info->brand_id) {
                    $is_offer_applicable = true;
                } elseif (!empty($offer->subcategory_id) && $offer->subcategory_id == $product_info->subcategory_id) {
                    $is_offer_applicable = true;
                } elseif (!empty($offer->category_id) && $offer->category_id == $product_info->category_id) {
                    $is_offer_applicable = true;
                }   

                if ($is_offer_applicable) {
                    if ($offer->offer_type === 'flat') {
                        $discounted_price = max(0, $original_price - intval($offer->discount_amount));
                    } elseif ($offer->offer_type === 'percentage') {
                        $discounted_price = max(0, $original_price - ($original_price * intval($offer->discount_amount) / 100));
                    }
                }
        }

        return response()->json([
            'orignal_price' => $original_price,
            'discounted_price' => $discounted_price,
            'product_stock' => $instock,
            'is_in_cart' => $is_in_cart
        ]);



      
    }
    public function productFavourite(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $product_id = $request->product_id;
        $user_id = $request->user_id;
        $status = $request->status;
        if ($status==0) {
            // echo "string";
            $productFavData = DB::table('favorite_product')->where('product_id',$product_id)->where('user_id',$user_id)->first();
            if (!empty($productFavData)) {
            // echo "string1";
                $updatepsw = FavoriteProduct::where('user_id', $user_id)->where('product_id',$product_id)->update(array(
                   'status' => 1,
                ));
            }else{
            // echo "string3";
                $fav_product = new FavoriteProduct();
                $fav_product->product_id = $product_id;
                $fav_product->user_id = $user_id;
                $fav_product->status = 1;
                $fav_product->save();
            }
            
            // Alert::success('Success',__('backend.product_fav_successfully'));
        return response()->json(['success' => 'true']);
        }else{
            // echo "string4";
            $updatepsw = FavoriteProduct::where('user_id', $user_id)->where('product_id',$product_id)->update(array(
                   'status' => 2,
                ));
            // Alert::success('Success',__('backend.product_unfav_successfully'));
        return response()->json(['success' => 'true']);
        }
    }        

    public function checkCartVariant($array, $string){
        foreach($array as $key => $result){            
            if($key==$string){
                echo "Matched";
            }else{
                echo "Not Matched";
            }
        }
    }
   
    public function productCartAdd(Request $request){
        $product_id = base64_decode($request->product_id);
        $variant_id = $request->variantId;   
        $quantity = (int) $request->quantity;
        $bogo_status = (int) $request->bogo_status;
        $offer_status = (int) $request->offer_status;

        // Get current Offer
        $today = Carbon::now()->toDateString();
        $currentOffer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->first(); 
        $currentOfferType=null;
        $currentOfferAmount=null;

        if($currentOffer)
        {
             $currentOfferType=$currentOffer->offer_type;
             $currentOfferAmount=$currentOffer->dis_amount;
        }

        if(Auth::guard('user')->user()==""){
            $get_cart_array = $request->session()->get('cart_info');

            $new_item = [
                'quantity' => $quantity,
                'is_bogo' => $bogo_status
            ];
             
            if (!$bogo_status && $offer_status) {
                $new_item['offer_type'] = $currentOfferType;
                $new_item['discount_amount'] = $currentOfferAmount;
                $new_item['is_offer'] = $offer_status;
            }


            if($get_cart_array==""){
                /*---Creating new product array for cart.--*/

                // $cart_info = array($product_id=>array($variant_id=>$quantity));
                $cart_info = array($product_id=>array($variant_id=>$new_item));

                Session::put('cart_info',$cart_info);
            }else{
                /*---session have cart values if product is exits.--*/
                if(array_key_exists($product_id,$get_cart_array)){
                    foreach($get_cart_array as $key => $variant_array){     
                        if($key==$product_id && array_key_exists($variant_id,$variant_array)){                       
                            $new_array = array($product_id=>array($variant_id=>$new_item));
                            $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
                            $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
                            $result = array_replace_recursive($get_cart_array, $new_array);                      
                            Session::put('cart_info',$result);
                        }else{
                            $new_array = array($product_id=>array($variant_id=>$new_item));
                            $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
                            $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
                            $result = array_replace_recursive($get_cart_array, $new_array);                      
                            Session::put('cart_info',$result);
                        }
                    }
                }else{
                    /*---combine if already product in cart and add new cart product in sesstion.--*/
                    $new_array = array($product_id=>array($variant_id=>$new_item));
                    $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
                    $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
                    $result = array_replace_recursive($get_cart_array, $new_array);
                    Session::put('cart_info',$result);          
                }
            }
            $get_cart_count = count(Session::get('cart_info'));
            return response()->json(['success' => 'true','cart_count'=>$get_cart_count]);
        }else{
            $user_id = Auth::guard('user')->user()->id;
            /*-- Get product variant details---*/
            $product_variant_details = DB::table('product')->leftjoin('product_variants', 'product_variants.product_id', '=', 'product.id')->where('product.id', $product_id)->where('product_variants.id', $variant_id)->select('product_variants.*')->first();
                if($product_variant_details->variant_discounted_price=='' || $product_variant_details->variant_discounted_price==0.0){                    
                    $total_price = $quantity * (isset($product_variant_details->variant_price) ? $product_variant_details->variant_price : '0');
                }else{
                    $total_price = $quantity * (isset($product_variant_details->variant_discounted_price) ? $product_variant_details->variant_discounted_price : '0');
                }

            /*--- Check user request data is in cart or not ---*/
            $cartData = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $user_id)->where('cart.product_id', $product_id)->where('product_variant_id', $variant_id)->where('cart.status', 1)->select('cart.*')->first();

            // $apply_bogo = ($cartData && $cartData->is_bogo == 1) ? 0 : $bogo_status;

            if (!empty($cartData)) {
                $product_price = $product_variant_details->variant_price;

                $offer_price = $product_variant_details->variant_discounted_price;

                $updateData = [
                    'product_price' => $product_price,
                    'offer_price' => $offer_price,
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                    'is_bogo' => $bogo_status
                ];

                if (!$bogo_status && $offer_status) {
                    $updateData['offer_type'] = $currentOfferType;
                    $updateData['discount_amount'] = $currentOfferAmount;
                    $updateData['is_offer'] = $offer_status;
                } else {
                    $updateData['offer_type'] = null;
                    $updateData['discount_amount'] = null;
                    $updateData['is_offer'] = 0;
                }

                $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $product_id)->where('product_variant_id', $variant_id)->update( $updateData);
            
            } else {
                $product_price = $product_variant_details->variant_price;
                $offer_price = $product_variant_details->variant_discounted_price;
               
                $uniqid = uniqid();
                $cart = new Cart();
                $cart->uniqid = $uniqid;
                $cart->product_id = $product_id;
                $cart->product_variant_id = $product_variant_details->id;
                $cart->product_price = $product_price;
              //  $cart->product_price = $product_variant_details->variant_discounted_price;
                $cart->user_id = $user_id;
                $cart->total_price =  $total_price;
                $cart->offer_price =  $offer_price;
                $cart->order_type = 1;
                $cart->quantity = $quantity ;
                $cart->is_bogo = $bogo_status;
                
                if (!$bogo_status && $offer_status) {
                    $cart->offer_type = $currentOfferType;
                    $cart->discount_amount = $currentOfferAmount;
                    $cart->is_offer = $offer_status;
                } else {
                    $cart->offer_type = null;
                    $cart->discount_amount = null;
                    $cart->is_offer = 0;
                }
                $cart->status = 1;
                $cart->save();
            }
            $totalCount = \DB::table('cart')->where('user_id',$user_id)->where('status','1')->count();
            return response()->json(['success' => 'true', 'cart_count' => $totalCount ?? 0]);
        }       
    }

    public function checkProductQty(Request $request){
        $variant_id = $request->variant_id;
        $currentVal = $request->currentVal;
        $product_variant = ProductVariants::where('id',$variant_id)->first();
       // dd($product_variant);
        if($product_variant->available_qty <=$currentVal){
            return response()->json(['message' => 'Product variant is now same qty','is_equal'=>'1']);
        }
    }

    
}
