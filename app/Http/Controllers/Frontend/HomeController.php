<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MainUser;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Categories;
use App\Models\SubCategories;
use Illuminate\Validation\Rule;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Auth;
use Alert;
use DB;
use Mail;
use Carbon\Carbon;
use Storage;
Use Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Level;
use Helper;
use Illuminate\Support\Str;
use App\Models\Bogo;
use App\Models\Offers;


class HomeController extends Controller
{

    public function __construct()
    {  //Session::flush();
        // Session::forget('test1');
        // $test = Helper::getCartQuantity(2);
        ///dd(Session::all());
        //$car_old_array = Session::all();
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
    }

    public function index()
    {
        $offer_product = Product::activeProductsBasedOnRelations()->where('offer',1)->limit('20')->orderby('updated_at','desc')->get();
        $best_seller_product = Product::activeProductsBasedOnRelations()->where('is_product_bestseller',1)->limit('20')->orderby('updated_at','desc')->get();


        // Bogo & offer status for offer and best Seller
        $today = Carbon::now()->toDateString();


         // Offer Bogo
        foreach ($offer_product as $bogoOffer) {
            $bogo = Bogo::where('status', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->where(function ($query) use ($bogoOffer) {
                    $query->where('product_id', $bogoOffer->id)
                        ->orWhere(function ($q) use ($bogoOffer) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $bogoOffer->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($bogoOffer) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $bogoOffer->category_id);
                        });

                    if (!empty($bogoOffer->brand_id)) {
                        $query->orWhere('brand_id', $bogoOffer->brand_id);
                    }
                })
                ->first();

            $bogoOffer->bogo_status = $bogo ? true : false; 


            // Offer Check
              $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($bogoOffer) {
                    $query->where('product_id', $bogoOffer->id)
                        ->orWhere(function ($q) use ($bogoOffer) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $bogoOffer->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($bogoOffer) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $bogoOffer->category_id);
                        });

                    if (!empty($bogoOffer->brand_id)) {
                        $query->orWhere('brand_id', $bogoOffer->brand_id);
                    }
                })
                ->first();

                if ($offer) {
                    $bogoOffer->offer_status = true;
                    $bogoOffer->discount_amount = $offer->dis_amount;
                    $bogoOffer->offer_type = $offer->offer_type;
                } else {
                    $bogoOffer->offer_status = false;
                    $bogoOffer->discount_amount = 0;
                    $bogoOffer->offer_type = null;
                }

        }

        // Best Seller bogo
        foreach ($best_seller_product as $best) {
            $bogo = Bogo::where('status', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->where(function ($query) use ($best) {
                    $query->where('product_id', $best->id)
                        ->orWhere(function ($q) use ($best) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $best->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($best) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $best->category_id);
                        });

                    if (!empty($best->brand_id)) {
                        $query->orWhere('brand_id', $best->brand_id);
                    }
                })
                ->first();

            $best->bogo_status = $bogo ? true : false; 


             // Offer Check
              $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($best) {
                    $query->where('product_id', $best->id)
                        ->orWhere(function ($q) use ($best) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $best->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($best) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $best->category_id);
                        });

                    if (!empty($best->brand_id)) {
                        $query->orWhere('brand_id', $best->brand_id);
                    }
                })
                ->first();

                if ($offer) {
                    $best->offer_status = true;
                    $best->discount_amount = $offer->dis_amount;
                    $best->offer_type = $offer->offer_type;
                } else {
                    $best->offer_status = false;
                    $best->discount_amount = 0;
                    $best->offer_type = null;
                }
        }
    
        $blogs = Blog::where('status',1)->limit(3)->orderby('created_at','desc')->get();
        $banners = Banner::where('status',1)->limit(6)->orderby('created_at','desc')->where('highlight',null)->where('offer',null)->get();
        $banners_highlight = Banner::where('status',1)->where('highlight',1)->limit(6)->orderby('created_at','desc')->get();
        $banners_offer = Banner::where('status',1)->where('offer',1)->limit(1)->orderby('created_at','desc')->latest()->first();
        $categories = Categories::where('status',1)->orderby('created_at','desc')->get();
       // $bannerData =  Banner::where('status',1)->get();
        return view("frontend.home",compact('offer_product','best_seller_product','blogs','banners','banners_highlight','banners_offer','categories'));
    }

    private function getFilteredProducts($product_id = null, $brand_id = null, $subcategory_id = null, $category_id = null)
    {
        $query = DB::table('product as p')
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
                'p.brand_id'
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
            ->join(DB::raw('(SELECT *, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
                function ($join) {
                    $join->on('p.id', '=', 'pv.product_id');
                })
            ->where('p.status', 1);

            // Priority-based filtering
            if (!empty($product_id)) {
                $query->where('p.id', $product_id);
            } elseif (!empty($brand_id)) {
                $query->where('b.id', $brand_id);
            } elseif (!empty($subcategory_id)) {
                $query->where('sc.id', $subcategory_id);
            } elseif (!empty($category_id)) {
                $query->where('c.id', $category_id);
            }

            return $query->groupBy('p.id')->orderBy('p.id', 'desc')->get();
    }

    public function special(Request $request)
    {
        $user_id = $this->user_id;

        $categoryData = Categories::where('status', 1)->orderBy('id', 'ASC');

        $subcategoryData = SubCategories::withWhereHas('category', function ($query) {
            $query->where('status', 1);
        })->where('status', 1)->orderBy('id', 'ASC');

        $categories = Categories::with(['subcategory'=>function ($query){
            $query->where('status',1);
        }])->where('status',1)->get();

        $brandData = Brand::where('status',1)->orderBy('title','asc')->get();

        $product_val =  new Product;
        $product_max_price = $product_val->getproductMaxPrice();
        $product_min_price = $product_val->getproductMinPrice();

        // offer details
        $today = Carbon::now()->toDateString();
        $offer = Offers::where('status', 1)
                ->whereDate('expiry_date', '>=', $today)
                ->first(); 

        $offer_category_id = null;
        $offer_subcategory_id = null;
        $offer_product_id = null;
        $offer_brand_id = null;

        if ($offer) {
            if (!empty($offer->product_id)) {
                $offer_product_id = $offer->product_id;

            } elseif (!empty($offer->brand_id)) {
                $offer_brand_id = $offer->brand_id;

            } elseif (!empty($offer->category_id) && !empty($offer->subcategory_id)) {
                $offer_subcategory_id = $offer->subcategory_id;

            } elseif (!empty($offer->category_id)) {
                $offer_category_id = $offer->category_id;
            }
        }

        $offerProductDataFilter=$this->getFilteredProducts(
                                    $offer_product_id,
                                    $offer_brand_id,
                                    $offer_subcategory_id,
                                    $offer_category_id
                                );

        // Bogo details
        $bogo = Bogo::where('status', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first();

        $bogo_category_id = null;
        $bogo_subcategory_id = null;
        $bogo_product_id = null;
        $bogo_brand_id = null;

        if ($bogo) {
            if (!empty($bogo->product_id)) {
                $bogo_product_id = $bogo->product_id;

            } elseif (!empty($bogo->brand_id)) {
                $bogo_brand_id = $bogo->brand_id;

            } elseif (!empty($bogo->category_id) && !empty($bogo->subcategory_id)) {
                $bogo_subcategory_id = $bogo->subcategory_id;

            } elseif (!empty($bogo->category_id)) {
                $bogo_category_id = $bogo->category_id;
            }
        }

        $bogoProductDataFilter = $this->getFilteredProducts(
                                    $bogo_product_id,
                                    $bogo_brand_id,
                                    $bogo_subcategory_id,
                                    $bogo_category_id
                                );


        $limit = 16;

        $has_offer_products = !empty($offer) && $offerProductDataFilter->count() > 0;
        $has_bogo_products = !empty($bogo) && $bogoProductDataFilter->count() > 0;

        // ---- Offer Products Pagination ----
        $offer_total_product_count = count($offerProductDataFilter);
        $offerProductData = $offerProductDataFilter->take($limit);

        if ($offer) {
            $offerProductData->transform(function ($product) use ($offer) {
                $product->offer_type = $offer->offer_type;
                $product->discount_amount = $offer->dis_amount;
                $product->offer_status = $offer ? true : false;
                return $product;
            });
        }

        $offer_showing_product_count = 0;
        if($offer_total_product_count!=0 && $offer_total_product_count >= $limit ){
            $offer_showing_product_count = $limit;
        }elseif($offer_total_product_count <= $limit ){
            $offer_showing_product_count = $offer_total_product_count;
        }

        // ---- BOGO Products Pagination ----
        $bogo_total_product_count = count($bogoProductDataFilter);
        $bogoProductData = $bogoProductDataFilter->take($limit);

        if ($bogo) {
            $bogoProductData->transform(function ($product) use ($bogo) {
                $product->bogo_status = $bogo ? true : false;
                return $product;
            });
        }

        $bogo_showing_product_count = 0;
        if($bogo_total_product_count!=0 && $bogo_total_product_count >= $limit ){
            $bogo_showing_product_count = $limit;
        }elseif($bogo_total_product_count <= $limit ){
            $bogo_showing_product_count = $bogo_total_product_count;
        }


        return view("frontend.product.offer-list",compact('categoryData','subcategoryData','offerProductDataFilter','bogoProductDataFilter','offerProductData','bogoProductData','user_id','brandData','product_max_price','product_min_price','offer_total_product_count','bogo_total_product_count','offer_showing_product_count','bogo_showing_product_count','categories','has_offer_products', 'has_bogo_products',));

    }

     public function loadfilterofferlist(Request $request)
    {
        $product_last_id_for_pagination = $request->product_last_id;
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

        $minprice = ($request->min_price)?$request->min_price:'';
        $maxprice = ($request->max_price)?$request->max_price:'';

          // offer details
        $today = Carbon::now()->toDateString();
        $offer = Offers::where('status', 1)
                ->whereDate('expiry_date', '>=', $today)
                ->first(); 

        $offer_category_id = null;
        $offer_subcategory_id = null;
        $offer_product_id = null;
        $offer_brand_id = null;

        if ($offer) {
            if (!empty($offer->product_id)) {
                $offer_product_id = $offer->product_id;

            } elseif (!empty($offer->brand_id)) {
                $offer_brand_id = $offer->brand_id;

            } elseif (!empty($offer->category_id) && !empty($offer->subcategory_id)) {
                $offer_subcategory_id = $offer->subcategory_id;

            } elseif (!empty($offer->category_id)) {
                $offer_category_id = $offer->category_id;
            }
        }

        $offerProductDataFilter=DB::table('product as p')
                    ->select('p.id as product_id','p.average_rating','p.product_name','p.product_name_fr','pi.image', 'pv.*', 'pv.final_price','mvp.count','p.category_id','p.subcategory_id','p.brand_id')
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
                    ->where('p.status', '=', 1)
                    ->groupBy('p.id');


        if($category_ids!=null && $subcategory_ids ==null){
            $offerProductDataFilter->whereIn('sc.category_id', $category_ids);
        }
        else if($category_ids!=null && ($subcategory_ids && $subcategory_ids != null)){
            $offerProductDataFilter->where(function ($query) use ($subcategory_ids,$category_ids){
                $query->whereIn('sc.id', $subcategory_ids)
                      ->orWhereIn('sc.category_id', $category_ids);
            });
        }
        else if($category_ids==null && ($subcategory_ids && $subcategory_ids != null)){
            $offerProductDataFilter->whereIn('sc.id', $subcategory_ids);
        }


        if($brand_ids && $brand_ids != null){
            $offerProductDataFilter->whereIn('b.id', $brand_ids);
        }

        if(($minprice && $minprice != null) && ($maxprice && $maxprice != null)){                
            $offerProductDataFilter->whereBetween('pv.final_price', [$minprice, $maxprice]);
        }

        if ($offer_product_id && $offer_product_id!=null) {
             $offerProductDataFilter->where('p.id', '=', $offer_product_id);
        } 

        if ($offer_brand_id && $offer_brand_id!=null) {
             $offerProductDataFilter->where('b.id', '=', $offer_brand_id);
        } 

        if ($offer_subcategory_id && $offer_subcategory_id!=null) {
             $offerProductDataFilter->where('sc.id', '=', $offer_subcategory_id);
        } 

        if ($offer_category_id && $offer_category_id!=null) {
             $offerProductDataFilter->where('c.id', '=', $offer_category_id);
        } 

        $sort_by = $request->sort_by;
        if($sort_by==1){
            $offerProductDataFilter = $offerProductDataFilter->orderBy('p.id', 'desc');
        }elseif($sort_by==2){
            $offerProductDataFilter =  $offerProductDataFilter->orderBy('pv.final_price', 'desc');
        }elseif($sort_by==3){
            $offerProductDataFilter = $offerProductDataFilter->orderBy('pv.final_price', 'asc');
        }elseif($sort_by==4){
           $sorting_data =  ['mvp.count', 'desc'];
            // $offerProductDataFilter->orderBy('mvp.count', 'desc');
        }else{
            $offerProductDataFilter = $offerProductDataFilter->orderBy('p.id', 'desc');
        }


        $limit = 16;
        $current_page_count = $request->current_page_count;
        $offerProductDataFilter  = $offerProductDataFilter->get();  

        $offer_total_product_count = count($offerProductDataFilter);
        $offerProductData = $offerProductDataFilter->skip($current_page_count)->take($limit);

        if ($offer) {
            $offerProductData->transform(function ($product) use ($offer) {
                $product->offer_type = $offer->offer_type;
                $product->discount_amount = $offer->dis_amount;
                $product->offer_status = $offer ? true : false;
                return $product;
            });
        }


        $offer_showing_product_count = 0;
        if($offer_total_product_count!=0 && $offer_total_product_count >= $limit ){
            $offer_showing_product_count = $limit;
        }elseif($offer_total_product_count <= count($offerProductData) ){
            $offer_showing_product_count = $offer_total_product_count;
        }

        $data = ['offer_total_product_count'=>$offer_total_product_count,'offer_showing_product_count'=>$offer_showing_product_count,'offerProductData'=>$offerProductData];

        return view("frontend.product.ajax_filter_offer",$data );

    }

      public function loadfilterbogolist(Request $request)
    {
        $product_last_id_for_pagination = $request->product_last_id;
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

        $minprice = ($request->min_price)?$request->min_price:'';
        $maxprice = ($request->max_price)?$request->max_price:'';

        // Bogo details
        $today = Carbon::now()->toDateString();
        $bogo = Bogo::where('status', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first();

        $bogo_category_id = null;
        $bogo_subcategory_id = null;
        $bogo_product_id = null;
        $bogo_brand_id = null;

        if ($bogo) {
            if (!empty($bogo->product_id)) {
                $bogo_product_id = $bogo->product_id;

            } elseif (!empty($bogo->brand_id)) {
                $bogo_brand_id = $bogo->brand_id;

            } elseif (!empty($bogo->category_id) && !empty($bogo->subcategory_id)) {
                $bogo_subcategory_id = $bogo->subcategory_id;

            } elseif (!empty($bogo->category_id)) {
                $bogo_category_id = $bogo->category_id;
            }
        }

        $bogoProductDataFilter=DB::table('product as p')
                    ->select('p.id as product_id','p.average_rating','p.product_name','p.product_name_fr','pi.image', 'pv.*', 'pv.final_price','mvp.count','p.category_id','p.subcategory_id','p.brand_id')
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
                    ->where('p.status', '=', 1)
                    ->groupBy('p.id');


        if($category_ids!=null && $subcategory_ids ==null){
            $bogoProductDataFilter->whereIn('sc.category_id', $category_ids);
        }
        else if($category_ids!=null && ($subcategory_ids && $subcategory_ids != null)){
            $bogoProductDataFilter->where(function ($query) use ($subcategory_ids,$category_ids){
                $query->whereIn('sc.id', $subcategory_ids)
                      ->orWhereIn('sc.category_id', $category_ids);
            });
        }
        else if($category_ids==null && ($subcategory_ids && $subcategory_ids != null)){
            $bogoProductDataFilter->whereIn('sc.id', $subcategory_ids);
        }


        if($brand_ids && $brand_ids != null){
            $bogoProductDataFilter->whereIn('b.id', $brand_ids);
        }

        if(($minprice && $minprice != null) && ($maxprice && $maxprice != null)){                
            $bogoProductDataFilter->whereBetween('pv.final_price', [$minprice, $maxprice]);
        }

        if ($bogo_product_id && $bogo_product_id!=null) {
             $bogoProductDataFilter->where('p.id', '=', $bogo_product_id);
        } 

        if ($bogo_brand_id && $bogo_brand_id!=null) {
             $bogoProductDataFilter->where('b.id', '=', $bogo_brand_id);
        } 

        if ($bogo_subcategory_id && $bogo_subcategory_id!=null) {
             $bogoProductDataFilter->where('sc.id', '=', $bogo_subcategory_id);
        } 

        if ($bogo_category_id && $bogo_category_id!=null) {
             $bogoProductDataFilter->where('c.id', '=', $bogo_category_id);
        } 

        $sort_by = $request->sort_by;
        if($sort_by==1){
            $bogoProductDataFilter = $bogoProductDataFilter->orderBy('p.id', 'desc');
        }elseif($sort_by==2){
            $bogoProductDataFilter =  $bogoProductDataFilter->orderBy('pv.final_price', 'desc');
        }elseif($sort_by==3){
            $bogoProductDataFilter = $bogoProductDataFilter->orderBy('pv.final_price', 'asc');
        }elseif($sort_by==4){
           $sorting_data =  ['mvp.count', 'desc'];
            // $bogoProductDataFilter->orderBy('mvp.count', 'desc');
        }else{
            $bogoProductDataFilter = $bogoProductDataFilter->orderBy('p.id', 'desc');
        }


        $limit = 16;
        $current_page_count = $request->current_page_count;
        $bogoProductDataFilter  = $bogoProductDataFilter->get();  

        $bogo_total_product_count = count($bogoProductDataFilter);
    
        $bogoProductData = $bogoProductDataFilter->skip($current_page_count)->take($limit);

        if ($bogo) {
            $bogoProductData->transform(function ($product) use ($bogo) {
                $product->bogo_status = $bogo ? true : false;
                return $product;
            });
        }

        $bogo_showing_product_count = 0;
        if($bogo_total_product_count!=0 && $bogo_total_product_count >= $limit ){
            $bogo_showing_product_count = $limit;
        }elseif($bogo_total_product_count <= count($bogoProductData) ){
            $bogo_showing_product_count = $bogo_total_product_count;
        }

        $data = ['bogo_total_product_count'=>$bogo_total_product_count,'bogo_showing_product_count'=>$bogo_showing_product_count,'bogoProductData'=>$bogoProductData];

        return view("frontend.product.ajax_filter_bogo",$data );

    }


    public function changeLanguage(Request $request){
        $language  = $request->language;
        return session()->put('language', $language);
    }

    public function setTimeZone(Request $request){
        $timezone  = $request->timezone;
        return session()->put('timzone_in_session', $timezone);
    }

    // public function autoSuggestion(Request $request){ 
    //     $keyword =  $request->keyword;
    //     $enter =  $request->enter;
    //     $categoryData = DB::table('categories')->leftjoin('product','product.category_id','=','categories.id')->where('categories.status', 1)->orderBy('categories.id', 'asc')->where('categories.title', 'LIKE', '%'.$keyword.'%')->select('categories.*','product.id as product_id','product.category_id as product_category_id')->groupby('categories.id')->get();
    //    dd($categoryData);
    //   //  $data = compact('');
    //     //return view('frontend.ajax-home', $data)->render();
    // }

    public function subscribeEmail(Request $request)
    {
        //dd($request->email);
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:news_latter',
            ]);

        $data = [
                    'email' =>$request->email,
                    'status' => 1
                ];
    
        if ($validator->fails())
        {
            
            return response()->json($validator->errors(),422);
        }
        else
        {
            $query_insert = \DB::table('news_latter')->insert($data);

            // $logo = \Config::get('app.url').'public/assets/dashboard/images/liquor.png';
            // $url_link = \URL::to("/");
            // $url = $url_link . '/';
            // $email = $request->email;

            // $ismail = $this->attachment_email($email, $url, $logo);

            Alert::success(\Helper::language('success'), __(@Helper::language('newsletter_subscribed_message')));
            return response()->json(
                [
                    'success' => true,
                    'message' => @Helper::language('newsletter_subscribed_message')
                ]
            );
        }  
       //return back()->with('success', 'Your email is subscribed..!');
       return back()->with('success', @Helper::language('newsletter_subscribed_message'));
    }

    // public function attachment_email($email, $url, $logo) {

       
    //     $setting = Setting::find(1);
    //     $from_email = 'admin@vrinsoft.com';
       
    //    // $from_email = $setting['from_email'];
    //     $data = array('email' => $email, 'url' => $url,'id'=>'5','logo' => $logo, 'from_email' => $from_email);
       
    //     \Mail::send('subscribe_email', $data, function ($message) use ($data) {

    //     $message->to($data['email'], 'OnlyDance')->subject('Your Email has Subscribe with us!');
    //     //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

    //     $message->from($data['from_email'], 'OnlyDance');
        
    //     });

    // }


    public function autoSuggestion(Request $request)
    {   
        $keyword =  $request->keyword;
        $user_id = $this->user_id;

        $settings = Setting::find(1);

        $categoryData = Categories::where('status', 1)->orderBy('id', 'ASC');

        $subcategoryData = SubCategories::withWhereHas('category', function ($query) {
            $query->where('status', 1);
        })->where('status', 1)->orderBy('id', 'ASC');
        
        $productDataFilter = Product::withWhereHas('get_product_images', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_product_variants', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_category', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_subcategory', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_brand_details', function ($query) {
            $query->where('status', 1);
        })->where('status',1)->orderby('created_at','desc');

        $productQueryForCount = clone $productDataFilter;

        if(session::get('language')==2){
            $categoryData = $categoryData->where('title_fr', 'LIKE', '%'.$keyword.'%')->get();
            $subcategoryData = $subcategoryData->where('title_fr', 'LIKE', '%'.$keyword.'%')->get();
            $productDataFilter = $productDataFilter->where('product_name_fr', 'LIKE', '%'.$keyword.'%')->take(12)->get();
            $productCount = $productQueryForCount->where('product_name_fr', 'LIKE', '%'.$keyword.'%')->count();
        }else{            
            $categoryData = $categoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
            $subcategoryData = $subcategoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
            $productDataFilter = $productDataFilter->where('product_name', 'LIKE', '%'.$keyword.'%')->take(12)->get();
            $productCount = $productQueryForCount->where('product_name', 'LIKE', '%'.$keyword.'%')->count();
        }

        $data = compact('categoryData','subcategoryData','productDataFilter','settings','productCount','keyword');
        return view('frontend.searching', $data)->render();

    }
    

    // public function attachment_quote_email($user_name,$sendname,$sendemail,$category_name,$post_code,$description,$image_name)
    // {
    //     $setting = Setting::find(1);
    //     $from_email = $setting['mail_no_replay'];
    //     $emailtemp = Emailtemplate::find('10');

    //     $data = array('user_name' => $user_name, 'sendname' => $sendname,'sendemail'=>$sendemail,'category_name'=>$category_name,'post_code'=>$post_code,'description'=>$description,'image_name'=>$image_name,'id'=>10,'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);

    //     Mail::send('emails.quote', $data, function ($message) use ($data) {

    //          $message->to($data['sendemail'], $data['title'])->subject($data['subject']);
    //     //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

    //     $message->from($data['from_email'], $data['support_name']);
    //     });
    // }

    // public function UserPromotedProduct(){
    //     // echo "string";exit();
    //      $user_id = $this->user_id;
    //      $setting = Setting::find(1);

    //    $promoteData = DB::table('promoted_product')->leftjoin('product','product.id','=','promoted_product.product_id')->where('promoted_product.status',1)->select('product.*')->orderby('promoted_product.id','DESC')->paginate(8);

    //    $productDataCount = DB::table('promoted_product')->leftjoin('product','product.id','=','promoted_product.product_id')->where('promoted_product.status',1)->select('product.*')->orderby('promoted_product.id','DESC')->count();
    //    // $promoteDataCount = $productDataCount->count();
    //    // echo "<pre>";print_r($productDataCount);exit();

    //    return view("frontEnd.product.promoted-product",compact('promoteData','user_id','productDataCount','setting'));

    // }

    public function dashboardFilterCategory(Request $request,$id)
    {
        $category_id = $request->id;
        $user_id = $this->user_id;

        $categoryDetails = Categories::where('id',$category_id)->first();

        $category_data = DB::table('product')->where('product.category_id',$category_id)->where('status',1)->where('is_admin_approve','=',1)->paginate(8);
        return view("frontend.product.product-list",compact('category_data','categoryDetails','user_id'));
    }
   
}
