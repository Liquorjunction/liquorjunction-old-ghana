<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Label;
use App\Models\Setting;
use App\Models\MainUser;
use App\Models\EmailTemplate;
use App\Models\FavoriteProduct;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Brand;
use App\Models\MostViewProduct;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductVariants;
use App\Models\Promocode;
use App\Models\Uofs;
use App\Models\Rating;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mail;
use Hash;
use Helper;
use Auth;
use DB;
use Carbon\Carbon;
use App\Models\Bogo;
use App\Models\Offers;
use App\Models\LoyaltyPoints;

class ProductController extends Controller
{
    private $uploadPath = "uploads/quote";

    public function getUploadPath()
    {
        return $this->uploadPath;
    }
    


    // offer
    
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

        $result = [];
        $today = Carbon::now()->toDateString();
        $limit = 16;

        // offer details
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


        $has_offer_products = !empty($offer) && $offerProductDataFilter->count() > 0;
        $has_bogo_products = !empty($bogo) && $bogoProductDataFilter->count() > 0;

        $offer_total_product_count = count($offerProductDataFilter);
        $offerProductData = $offerProductDataFilter->take($limit);
        $offer_showing_product_count = min($limit, $offer_total_product_count);

        $bogo_total_product_count = $bogoProductDataFilter->count();
        $bogoProductData = $bogoProductDataFilter->take($limit);
        $bogo_showing_product_count = min($limit, $bogo_total_product_count);

        
        // Transform offer products
        $offerProductArr = [];
        foreach ($offerProductData as $product) {
            if ($request->language == 1 && !empty($product->product_name_fr)) {
                $title = $product->product_name_fr;
            } else {
                $title = $product->product_name ?? '';
            }

            $unit = Helper::getUnitById(@$product->variant_uof);
            $size = ($product->variant_size) ? $product->variant_size . ' ' . $unit : '';

            $image_path = file_exists(public_path('uploads/product/' . $product->image))
                ? asset('uploads/product/' . $product->image)
                : asset('assets/frontend/images/image-not-avilable.png');

            $is_favourite = 0;
            if (!empty($request->uniqid)) {
                $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
                if ($userData) {
                    $is_favourite = FavoriteProduct::where([
                        ['user_id', '=', $userData->id],
                        ['product_id', '=', $product->product_id],
                        ['status', '=', 1]
                    ])->exists() ? 1 : 0;
                }
            }

            $offerProductArr[] = [
                'product_id' => strval(@$product->product_id),
                'product_title' => strval($title),
                'product_rating' => strval(@$product->average_rating),
                'product_size' => strval($size),
                'product_image' => $image_path,
                'product_orignal_price' => $product->variant_price ?? '',
                'product_discounted_price' => (!empty($product->variant_discounted_price) && $product->variant_discounted_price != 0.00)
                    ? $product->variant_discounted_price
                    : null,
                'is_favourite' => $is_favourite,
                'variant_id' => strval(@$product->id),
                'product_available_qty' => $product->available_qty,
                'product_status' => $product->status,
                'product_category_id' => @$product->category_id,
                'product_subcategory_id' => $product->subcategory_id,
                'product_brand_id' => $product->brand_id,
                'offer_type' => $offer->offer_type ?? '',
                'discount_amount' => $offer->dis_amount ?? '',
                'offer_status' => true
            ];
        }

        // Transform BOGO products
        $bogoProductArr = [];
        foreach ($bogoProductData as $product) {
            if ($request->language == 1 && !empty($product->product_name_fr)) {
                $title = $product->product_name_fr;
            } else {
                $title = $product->product_name ?? '';
            }

            $unit = Helper::getUnitById(@$product->variant_uof);
            $size = ($product->variant_size) ? $product->variant_size . ' ' . $unit : '';

            $image_path = file_exists(public_path('uploads/product/' . $product->image))
                ? asset('uploads/product/' . $product->image)
                : asset('assets/frontend/images/image-not-avilable.png');

            $is_favourite = 0;
            if (!empty($request->uniqid)) {
                $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
                if ($userData) {
                    $is_favourite = FavoriteProduct::where([
                        ['user_id', '=', $userData->id],
                        ['product_id', '=', $product->product_id],
                        ['status', '=', 1]
                    ])->exists() ? 1 : 0;
                }
            }

            $bogoProductArr[] = [
                'product_id' => strval(@$product->product_id),
                'product_title' => strval($title),
                'product_rating' => strval(@$product->average_rating),
                'product_size' => strval($size),
                'product_image' => $image_path,
                'product_orignal_price' => $product->variant_price ?? '',
                'product_discounted_price' => (!empty($product->variant_discounted_price) && $product->variant_discounted_price != 0.00)
                    ? $product->variant_discounted_price
                    : null,
                'is_favourite' => $is_favourite,
                'variant_id' => strval(@$product->id),
                'product_available_qty' => $product->available_qty,
                'product_status' => $product->status,
                'product_category_id' => @$product->category_id,
                'product_subcategory_id' => $product->subcategory_id,
                'product_brand_id' => $product->brand_id,
                'bogo_status' => true
            ];
        }

        // Final response
        $response = [
            'offer' => [
                    'status' => !empty($offer),
                    'products' => $offerProductArr,
                    'total_count' => $offer_total_product_count,
                    'showing_count' => $offer_showing_product_count,
                ],
                'bogo' => [
                    'status' => !empty($bogo),
                    'products' => $bogoProductArr,
                    'total_count' => $bogo_total_product_count,
                    'showing_count' => $bogo_showing_product_count,
                ]
            ];

        $mainResult = [
            'code' => '1',
            'message' => 'success',
            'result' => $response
        ];

        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    }


    // public function getProductList(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'listing_type' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status_code' => strval(0),
    //             'error'=>$validator->messages(),
    //             'data' => null
    //         ], 200);
    //     }
        
    //     $brand_ids = array();
    //     $category_ids = array();
    //     $subcategory_ids = array();
    //     if($request->brand_ids){
    //         $brand_ids = explode(',',$request->brand_ids);
    //     }        
    //     if($request->category_ids){
    //         $category_ids = explode(',',$request->category_ids);
    //     }
    //     if($request->subcategory_ids){
    //         $subcategory_ids = explode(',',$request->subcategory_ids);
    //     }

    //     $subcategory = array();
    //     $minprice = ($request->min_price)?$request->min_price:'';
    //     $maxprice = ($request->max_price)?$request->max_price:'';

     
    //     $uniqid = $request->uniqid;
    //     $products =  DB::table('product as p')
    //                 ->select('p.id as productId','p.average_rating','p.product_name','p.product_name_fr','pi.image', 'pv.*', 'pv.final_price','mvp.count','p.offer','p.is_product_bestseller')
    //                 ->join('product_image as pi', 'p.id', '=', 'pi.product_id')
    //                 ->where('pi.status', '=', 1)
    //                 ->join('categories as c', 'p.category_id', '=', 'c.id')                    
    //                     ->where('c.status', '=', 1)
    //                 ->join('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id')
    //                     ->where('sc.status', '=', 1)
    //                 ->join('brand as b', 'p.brand_id', '=', 'b.id')
    //                     ->where('b.status', '=', 1)
    //                 ->join(
    //                     DB::raw('(SELECT *,id as product_variant_id, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
    //                     function ($join) {
    //                         $join->on('p.id', '=', 'pv.product_id');
    //                     }
    //                 )
    //                 ->leftjoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
    //                 ->where('p.status', '=', 1)
    //                 ->groupBy('p.id');
                                      
    //                 $list_type = $request->listing_type;
                   
                    
    //                 if($list_type==1){
    //                     $products = $products->where('p.offer',1);
    //                 }elseif($list_type==2){
    //                     $products = $products->where('p.is_product_bestseller',1);
    //                 }
                   

    //                 if($category_ids!=null && $subcategory_ids ==null){
    //                     $products->whereIn('sc.category_id', $category_ids);
    //                 }else if($category_ids!=null && ($subcategory_ids && $subcategory_ids != null)){
    //                     //$products->whereIn('sc.id', $subcategory_ids)->orwhereIn('sc.category_id', $category_ids);
    //                     $products->where(function ($query) use ($subcategory_ids,$category_ids){
    //                         $query->whereIn('sc.id', $subcategory_ids)
    //                               ->orWhereIn('sc.category_id', $category_ids);
    //                     });
    //                 }else if($category_ids==null && ($subcategory_ids && $subcategory_ids != null)){
    //                     $products->whereIn('sc.id', $subcategory_ids);
    //                 }

    //                 if($brand_ids && $brand_ids != null){
    //                     $products->whereIn('b.id', $brand_ids);
    //                 }

    //                 if(($minprice && $minprice != null) && ($maxprice && $maxprice != null)){                
    //                    // $products->whereBetween('pv.final_price', [$minprice, $maxprice]);
    //                    $products->whereBetween('pv.final_price',array((string) $minprice, (string) $maxprice));
    //                 }

    //                 $sort_by = $request->sort_by;
    //                 if($sort_by==1){
    //                     //$products = $products->orderBy('p.id', 'desc');
    //                     if($list_type==1 || $list_type==2 ){
    //                         $products = $products->orderBy('p.updated_at', 'desc');
    //                     }else{
    //                         $products = $products->orderBy('p.id', 'desc');
    //                     }
    //                 }elseif($sort_by==2){
    //                     $products =  $products->orderBy('pv.final_price', 'desc');
    //                 }elseif($sort_by==3){
    //                     $products = $products->orderBy('pv.final_price', 'asc');
    //                 }elseif($sort_by==4){
    //                    $sorting_data =  ['mvp.count', 'desc'];
    //                 }else{                        
    //                     if($list_type==1 || $list_type==2 ){
    //                         $products = $products->orderBy('p.updated_at', 'desc');
    //                     }else{
    //                         $products = $products->orderBy('p.id', 'desc');
    //                     }
    //                 }
        
    //     $limit = 16;     
    //     $products  = $products->get(); 
    //     $total_product_count = count($products);
    //     //if($list_type!=1 && $list_type!=2){
    //         $page = $request->page;
    //         if($page=='' || $page==1 ){
    //             $page = 1;
    //             $current_page_count = 0; 
    //         }else{                
    //             $current_page_count = (($page - 1) * $limit); 
    //         } 
    //        // dd($current_page_count);
    //         $productData = $products->skip($current_page_count)->take($limit);
               
    //     // }else{
    //     //       $productData  = $products;
    //     // }       
       
    //     $result = [];
    //     if($productData){
    //         $productDataArr = [];           
    //         foreach($productData as $data){
    //             if ($request->language == 1) {
    //                 $title = ($data->product_name_fr) ? $data->product_name_fr : $data->product_name;
    //             } else {
    //                 $title = $data->product_name ?? '';
    //             } 
    //             $product_image = $data->image;
    //             $product_unit = Helper::getUnitById(@$data->variant_uof);
    //             $product_size =  ($data->variant_size) ? $data->variant_size . ' ' . $product_unit : '' ;
    //             if(file_exists(public_path() . '/uploads/product/' . $product_image)){
    //                 $image_path =  asset('uploads/product/' . $product_image);
    //             }else{
    //                 $image_path =  asset('assets/frontend/images/image-not-avilable.png');
    //             }
    //             $is_favourite = 0;   
    //             if($request->uniqid != ''){
    //                 $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                    
    //                 if(!empty($userData)){
    //                     $favourite = FavoriteProduct::where('user_id', $userData->id)->where('product_id', $data->productId)->where('status', 1)->count();  
    //                     if($favourite ==1){
    //                         $is_favourite = 1;
    //                     }
    //                 }                    
    //             }

    //             $original_price = ($data->variant_price) ? $data->variant_price :'';
    //             $discounted_price = ($data->variant_discounted_price!='' && $data->variant_discounted_price!=0.00)? $data->variant_discounted_price :NULL;

    //             $productDataArr[] = [
    //                 'product_id'=>strval(@$data->productId),
    //                 'product_title'=>strval(@$title),
    //                 'product_rating'=>strval(@$data->average_rating),
    //                 'product_size' => strval(@$product_size),
    //                 'product_image'=> $image_path,
    //                 'product_orignal_price'=> $original_price,
    //                 'product_discounted_price'=>$discounted_price,
    //                 'is_favourite' => $is_favourite,
    //                 'variant_id'=>strval(@$data->product_variant_id)

    //             ];
    //         }        

    //         $result['code']     =    strval(1);
    //         $result['message']  =   'success';
    //         $result['result']  =   $productDataArr; 
    //         $result['total_product_count']  =  $total_product_count;
    //     }else{
    //         $result['code']     =   strval(0);
    //         $result['message']  =   'no_data_found';
    //         $result['result']   =   [];
    //     }
    
    //     $mainResult = $result;   
      
    //     return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
    // }
   

    // 11th Aug 2025 new
    // public function getProductList(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'listing_type' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status_code' => strval(0),
    //             'error'=>$validator->messages(),
    //             'data' => null
    //         ], 200);
    //     }
        
    //     $brand_ids = array();
    //     $category_ids = array();
    //     $subcategory_ids = array();
    //     if($request->brand_ids){
    //         $brand_ids = explode(',',$request->brand_ids);
    //     }        
    //     if($request->category_ids){
    //         $category_ids = explode(',',$request->category_ids);
    //     }
    //     if($request->subcategory_ids){
    //         $subcategory_ids = explode(',',$request->subcategory_ids);
    //     }

    //     $subcategory = array();
    //     $minprice = ($request->min_price)?$request->min_price:'';
    //     $maxprice = ($request->max_price)?$request->max_price:'';

     
    //     $uniqid = $request->uniqid;
    //     $products =  DB::table('product as p')
    //                 ->select('p.id as productId','p.average_rating','p.product_name','p.product_name_fr','pi.image', 'pv.*', 'pv.final_price','mvp.count','p.offer','p.is_product_bestseller','p.category_id','p.subcategory_id','p.brand_id')
    //                 ->join('product_image as pi', 'p.id', '=', 'pi.product_id')
    //                 ->where('pi.status', '=', 1)
    //                 ->join('categories as c', 'p.category_id', '=', 'c.id')                    
    //                     ->where('c.status', '=', 1)
    //                 ->join('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id')
    //                     ->where('sc.status', '=', 1)
    //                 ->join('brand as b', 'p.brand_id', '=', 'b.id')
    //                     ->where('b.status', '=', 1)
    //                 ->join(
    //                     DB::raw('(SELECT *,id as product_variant_id, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
    //                     function ($join) {
    //                         $join->on('p.id', '=', 'pv.product_id');
    //                     }
    //                 )
    //                 ->leftjoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
    //                 ->where('p.status', '=', 1)
    //                 ->groupBy('p.id');
                                      
    //                 $list_type = $request->listing_type;
    //                 $today = Carbon::now()->toDateString();
                    
    //                 if ($list_type == 1) {
    //                     $products = $products->where('p.offer', 1);
    //                 } elseif ($list_type == 2) {
    //                     $products = $products->where('p.is_product_bestseller', 1);
    //                 } elseif ($list_type == 4) {
    //                     // Only offer products
    //                     $offer = Offers::where('status', 1)
    //                         ->whereDate('expiry_date', '>=', $today)
    //                         ->first();

    //                     // Only bogo products
    //                     $bogo = Bogo::where('status', 1)
    //                         ->whereDate('start_date', '<=', $today)
    //                         ->whereDate('end_date', '>=', $today)
    //                         ->first();

    //                     if ($offer || $bogo) {
    //                         $products = $products->where(function ($query) use ($offer, $bogo) {
    //                             if ($offer) {
    //                                 $query->orWhere(function ($q) use ($offer) {
    //                                     if (!empty($offer->product_id)) {
    //                                         $q->where('p.id', $offer->product_id);
    //                                     } elseif (!empty($offer->brand_id)) {
    //                                         $q->where('b.id', $offer->brand_id);
    //                                     } elseif (!empty($offer->subcategory_id)) {
    //                                         $q->where('sc.id', $offer->subcategory_id);
    //                                     } elseif (!empty($offer->category_id)) {
    //                                         $q->where('c.id', $offer->category_id);
    //                                     }
    //                                 });
    //                             }

    //                             if ($bogo) {
    //                                 $query->orWhere(function ($q) use ($bogo) {
    //                                     if (!empty($bogo->product_id)) {
    //                                         $q->where('p.id', $bogo->product_id);
    //                                     } elseif (!empty($bogo->brand_id)) {
    //                                         $q->where('b.id', $bogo->brand_id);
    //                                     } elseif (!empty($bogo->subcategory_id)) {
    //                                         $q->where('sc.id', $bogo->subcategory_id);
    //                                     } elseif (!empty($bogo->category_id)) {
    //                                         $q->where('c.id', $bogo->category_id);
    //                                     }
    //                                 });
    //                             }
    //                         });
    //                     } else {
    //                         // No active offers or bogo
    //                         $products = $products->whereRaw('0 = 1');
    //                     }
    //                 } 

    //                 if($category_ids!=null && $subcategory_ids ==null){
    //                     $products->whereIn('sc.category_id', $category_ids);
    //                 }else if($category_ids!=null && ($subcategory_ids && $subcategory_ids != null)){
    //                     $products->where(function ($query) use ($subcategory_ids,$category_ids){
    //                         $query->whereIn('sc.id', $subcategory_ids)
    //                               ->orWhereIn('sc.category_id', $category_ids);
    //                     });
    //                 }else if($category_ids==null && ($subcategory_ids && $subcategory_ids != null)){
    //                     $products->whereIn('sc.id', $subcategory_ids);
    //                 }

    //                 if($brand_ids && $brand_ids != null){
    //                     $products->whereIn('b.id', $brand_ids);
    //                 }

    //                 if(($minprice && $minprice != null) && ($maxprice && $maxprice != null)){                
    //                    $products->whereBetween('pv.final_price',array((string) $minprice, (string) $maxprice));
    //                 }

    //                 $sort_by = $request->sort_by;
    //                 if($sort_by==1){
    //                     if($list_type==1 || $list_type==2 ){
    //                         $products = $products->orderBy('p.updated_at', 'desc');
    //                     }else{
    //                         $products = $products->orderBy('p.id', 'desc');
    //                     }
    //                 }elseif($sort_by==2){
    //                     $products =  $products->orderBy('pv.final_price', 'desc');
    //                 }elseif($sort_by==3){
    //                     $products = $products->orderBy('pv.final_price', 'asc');
    //                 }elseif($sort_by==4){
    //                    $sorting_data =  ['mvp.count', 'desc'];
    //                 }else{                        
    //                     if($list_type==1 || $list_type==2 || $list_type==4){
    //                         $products = $products->orderBy('p.updated_at', 'desc');
    //                     }else{
    //                         $products = $products->orderBy('p.id', 'desc');
    //                     }
    //                 }
        
    //     $limit = 16;     
    //     $products  = $products->get(); 
    //     $total_product_count = count($products);
    //         $page = $request->page;
    //         if($page=='' || $page==1 ){
    //             $page = 1;
    //             $current_page_count = 0; 
    //         }else{                
    //             $current_page_count = (($page - 1) * $limit); 
    //         } 
    //         $productData = $products->skip($current_page_count)->take($limit);
               
    //     $result = [];
    //     if($productData){
    //         $productDataArr = [];           
    //         foreach($productData as $data){
    //             if ($request->language == 1) {
    //                 $title = ($data->product_name_fr) ? $data->product_name_fr : $data->product_name;
    //             } else {
    //                 $title = $data->product_name ?? '';
    //             } 
    //             $product_image = $data->image;
    //             $product_unit = Helper::getUnitById(@$data->variant_uof);
    //             $product_size =  ($data->variant_size) ? $data->variant_size . ' ' . $product_unit : '' ;
    //             if(file_exists(public_path() . '/uploads/product/' . $product_image)){
    //                 $image_path =  asset('uploads/product/' . $product_image);
    //             }else{
    //                 $image_path =  asset('assets/frontend/images/image-not-avilable.png');
    //             }
    //             $is_favourite = 0;   
    //             if($request->uniqid != ''){
    //                 $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                    
    //                 if(!empty($userData)){
    //                     $favourite = FavoriteProduct::where('user_id', $userData->id)->where('product_id', $data->productId)->where('status', 1)->count();  
    //                     if($favourite ==1){
    //                         $is_favourite = 1;
    //                     }
    //                 }                    
    //             }

    //             $inCart=0;
    //              if($request->uniqid != ''){
    //                 $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                    
    //                 if(!empty($userData)){
    //                     $cartItem = DB::table('cart')->where('user_id',$userData->id)->where('product_id', $data->productId)->where('status', 1)->first();
    //                     if($cartItem){
    //                         $inCart = 1;
    //                     }
    //                 }                    
    //             }

    //             $original_price = ($data->variant_price) ? $data->variant_price :'';
    //             $discounted_price = ($data->variant_discounted_price!='' && $data->variant_discounted_price!=0.00)? $data->variant_discounted_price :NULL;

    //             // Offers
    //             $offer_status = false;
    //             $offer_type = null;
    //             $discount_amount = null;
    //             $offer_product_category_id=null;
    //             $offer_product_subcategory_id=null;
    //             $offer_product_brand_id=null;

    //             if ($list_type == 4 && $offer && (
    //                 ($offer->product_id && $offer->product_id == $data->productId) ||
    //                 ($offer->brand_id && $offer->brand_id == $data->brand_id) ||
    //                 ($offer->subcategory_id && $offer->subcategory_id == $data->subcategory_id) ||
    //                 ($offer->category_id && $offer->category_id == $data->category_id)
    //             )) {
    //                 $offer_status = true;
    //                 $offer_type = $offer->offer_type ?? null;
    //                 $discount_amount = $offer->dis_amount ?? null;
    //                 $offer_product_category_id=$data->category_id;
    //                 $offer_product_subcategory_id=$data->subcategory_id;
    //                 $offer_product_brand_id=$data->brand_id;
    //             }

    //             $bogo_status = false;
    //             $bogo_product_category_id=null;
    //             $bogo_product_subcategory_id=null;
    //             $bogo_product_brand_id=null;

    //             if ($list_type == 4 && $bogo && (
    //                 ($bogo->product_id && $bogo->product_id == $data->productId) ||
    //                 ($bogo->brand_id && $bogo->brand_id == $data->brand_id) ||
    //                 ($bogo->subcategory_id && $bogo->subcategory_id == $data->subcategory_id) ||
    //                 ($bogo->category_id && $bogo->category_id == $data->category_id)
    //             )) {
    //                 $bogo_status = true;
    //                 $bogo_product_category_id=$data->category_id;
    //                 $bogo_product_subcategory_id=$data->subcategory_id;
    //                 $bogo_product_brand_id=$data->brand_id;
    //             }

    //             $productArr = [
    //                 'product_id'=>strval(@$data->productId),
    //                 'product_title'=>strval(@$title),
    //                 'product_rating'=>strval(@$data->average_rating),
    //                 'product_size' => strval(@$product_size),
    //                 'product_image'=> $image_path,
    //                 'product_orignal_price'=> $original_price,
    //                 'product_discounted_price'=>$discounted_price,
    //                 'is_favourite' => $is_favourite,
    //                 'inCart' => $inCart,
    //                 'variant_id'=>strval(@$data->product_variant_id)
    //             ];


    //             if($offer_status)
    //             {
    //                  $offer_label = '';
    //                  $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

    //                  if ($offer_type == 'flat') {
    //                     $offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
    //                  } elseif ($offer_type == 'percentage') {
    //                     $offer_label = $formatted_discount  . '% off';
    //                  }

    //                  $productArr['offer_status'] = true;
    //                  $productArr['offer_type'] = $offer_type;
    //                  $productArr['discount_amount'] =  $discount_amount;
    //                  $productArr['offer_label'] = $offer_label;
    //                  $productArr['product_category_id'] =  $offer_product_category_id;
    //                  $productArr['product_subcategory_id'] =  $offer_product_subcategory_id;
    //                  $productArr['product_brand_id'] =  $offer_product_brand_id;
    //             }

    //             if($bogo_status)
    //             {
    //                   $bogo_label = 'BOGO';

    //                   $productArr['bogo_status'] = true;
    //                   $productArr['bogo_label'] = $bogo_label;
    //                   $productArr['product_category_id'] =  $bogo_product_category_id;
    //                   $productArr['product_subcategory_id'] =  $bogo_product_subcategory_id;
    //                   $productArr['product_brand_id'] =  $bogo_product_brand_id;
    //             }

    //             $productDataArr[] = $productArr;
    //         }        

    //         $result['code']     =    strval(1);
    //         $result['message']  =   'success';
    //         $result['result']  =   $productDataArr; 
    //         $result['total_product_count']  =  $total_product_count;
    //     }else{
    //         $result['code']     =   strval(0);
    //         $result['message']  =   'no_data_found';
    //         $result['result']   =   [];
    //     }
    
    //     $mainResult = $result;   
      
    //     return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
    // }

    // 28 aug 2025
    public function getProductList(Request $request)
        {
            $validator = \Validator::make($request->all(), [
                'listing_type' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => strval(0),
                    'error'=>$validator->messages(),
                    'data' => null
                ], 200);
            }

            $brand_id=$request->brand_id;
            
            $brand_ids = array();
            $category_ids = array();
            $subcategory_ids = array();
            if($request->brand_ids){
                $brand_ids = explode(',',$request->brand_ids);
            }        
            if($request->category_ids){
                $category_ids = explode(',',$request->category_ids);
            }
            if($request->subcategory_ids){
                $subcategory_ids = explode(',',$request->subcategory_ids);
            }

            $subcategory = array();
            // $minprice = ($request->min_price)?$request->min_price:'';
            // $maxprice = ($request->max_price)?$request->max_price:'';

            $minprice = $request->min_price !== null ? (float)$request->min_price : null;
            $maxprice = $request->max_price !== null ? (float)$request->max_price : null;

        
            $uniqid = $request->uniqid;
            $products =  DB::table('product as p')
                        ->select('p.id as productId','p.average_rating','p.product_name','p.product_name_fr','pi.image', 'pv.*', 'pv.final_price','mvp.count','p.offer','p.is_product_bestseller','p.category_id','p.subcategory_id','p.brand_id')
                        ->join('product_image as pi', 'p.id', '=', 'pi.product_id')
                        ->where('pi.status', '=', 1)
                        ->join('categories as c', 'p.category_id', '=', 'c.id')                    
                            ->where('c.status', '=', 1)
                        ->join('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id')
                            ->where('sc.status', '=', 1)
                        ->join('brand as b', 'p.brand_id', '=', 'b.id')
                            ->where('b.status', '=', 1)
                        ->join(
                            DB::raw('(SELECT *,id as product_variant_id, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
                            function ($join) {
                                $join->on('p.id', '=', 'pv.product_id');
                            }
                        )
                        ->leftjoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
                        ->where('p.status', '=', 1)
                        ->groupBy('p.id');
                                        
                        $list_type = $request->listing_type;
                        $today = Carbon::now()->toDateString();
                        
                        if ($list_type == 1) {
                            $products = $products->where('p.offer', 1);
                        } elseif ($list_type == 2) {
                            $products = $products->where('p.is_product_bestseller', 1);
                        } else if($list_type == 3)
                        {
                            $products = $products->where('p.brand_id', $brand_id);
                        } elseif ($list_type == 4) {
                            // Only offer products
                            $offer = Offers::where('status', 1)
                                ->whereDate('expiry_date', '>=', $today)
                                ->first();

                            // Only bogo products
                            $bogo = Bogo::where('status', 1)
                                ->whereDate('start_date', '<=', $today)
                                ->whereDate('end_date', '>=', $today)
                                ->first();

                            if ($offer || $bogo) {
                                $products = $products->where(function ($query) use ($offer, $bogo) {
                                    if ($offer) {
                                        $query->orWhere(function ($q) use ($offer) {
                                            if (!empty($offer->product_id)) {
                                                $q->where('p.id', $offer->product_id);
                                            } elseif (!empty($offer->brand_id)) {
                                                $q->where('b.id', $offer->brand_id);
                                            } elseif (!empty($offer->subcategory_id)) {
                                                $q->where('sc.id', $offer->subcategory_id);
                                            } elseif (!empty($offer->category_id)) {
                                                $q->where('c.id', $offer->category_id);
                                            }
                                        });
                                    }

                                    if ($bogo) {
                                        $query->orWhere(function ($q) use ($bogo) {
                                            if (!empty($bogo->product_id)) {
                                                $q->where('p.id', $bogo->product_id);
                                            } elseif (!empty($bogo->brand_id)) {
                                                $q->where('b.id', $bogo->brand_id);
                                            } elseif (!empty($bogo->subcategory_id)) {
                                                $q->where('sc.id', $bogo->subcategory_id);
                                            } elseif (!empty($bogo->category_id)) {
                                                $q->where('c.id', $bogo->category_id);
                                            }
                                        });
                                    }
                                });
                            } else {
                                // No active offers or bogo
                                $products = $products->whereRaw('0 = 1');
                            }
                        } 

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
                        // $products->whereBetween('pv.final_price',array((string) $minprice, (string) $maxprice));
                        // }

                        if (is_numeric($minprice) && is_numeric($maxprice)) { 
                            $products->whereBetween('pv.final_price', [$minprice, $maxprice]);
                        }

                        $sort_by = $request->sort_by;
                        if($sort_by==1){
                            if($list_type==1 || $list_type==2 ){
                                $products = $products->orderBy('p.updated_at', 'desc');
                            }else{
                                $products = $products->orderBy('p.id', 'desc');
                            }
                        }elseif($sort_by==2){
                            $products =  $products->orderBy('pv.final_price', 'desc');
                        }elseif($sort_by==3){
                            $products = $products->orderBy('pv.final_price', 'asc');
                        }elseif($sort_by==4){
                        $sorting_data =  ['mvp.count', 'desc'];
                        }else{                        
                            if($list_type==1 || $list_type==2 || $list_type==4){
                                $products = $products->orderBy('p.updated_at', 'desc');
                            }else{
                                $products = $products->orderBy('p.id', 'desc');
                            }
                        }
            
            $limit = 16;     
            $products  = $products->get(); 
            $total_product_count = count($products);
                $page = $request->page;
                if($page=='' || $page==1 ){
                    $page = 1;
                    $current_page_count = 0; 
                }else{                
                    $current_page_count = (($page - 1) * $limit); 
                } 
                $productData = $products->skip($current_page_count)->take($limit);
                
            $result = [];
            if($productData){
                $productDataArr = [];           
                foreach($productData as $data){
                    if ($request->language == 1) {
                        $title = ($data->product_name_fr) ? $data->product_name_fr : $data->product_name;
                    } else {
                        $title = $data->product_name ?? '';
                    } 
                    $product_image = $data->image;
                    $product_unit = Helper::getUnitById(@$data->variant_uof);
                    $product_size =  ($data->variant_size) ? $data->variant_size . ' ' . $product_unit : '' ;
                    if(file_exists(public_path() . '/uploads/product/' . $product_image)){
                        $image_path =  asset('uploads/product/' . $product_image);
                    }else{
                        $image_path =  asset('assets/frontend/images/image-not-avilable.png');
                    }
                    $is_favourite = 0;   
                    if($request->uniqid != ''){
                        $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                        
                        if(!empty($userData)){
                            $favourite = FavoriteProduct::where('user_id', $userData->id)->where('product_id', $data->productId)->where('status', 1)->count();  
                            if($favourite ==1){
                                $is_favourite = 1;
                            }
                        }                    
                    }

                    $inCart=0;
                    if($request->uniqid != ''){
                        $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                        
                        if(!empty($userData)){
                            $cartItem = DB::table('cart')->where('user_id',$userData->id)->where('product_id', $data->productId)->where('status', 1)->first();
                            if($cartItem){
                                $inCart = 1;
                            }
                        }                    
                    }

                    $original_price = ($data->variant_price) ? $data->variant_price :'';
                    $discounted_price = ($data->variant_discounted_price!='' && $data->variant_discounted_price!=0.00)? $data->variant_discounted_price :NULL;

                    // Common Offers
                    $bogo = Bogo::where('status', 1)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->where(function ($query) use ($data) {
                            $query->where('product_id', $data->productId)
                                ->orWhere(function ($q) use ($data) {
                                    $q->whereNotNull('subcategory_id')
                                        ->where('subcategory_id', $data->subcategory_id);
                                })
                                ->orWhere(function ($q) use ($data) {
                                    $q->whereNull('subcategory_id')
                                        ->where('category_id', $data->category_id);
                                });

                            if (!empty($data->brand_id)) {
                                $query->orWhere('brand_id', $data->brand_id);
                            }
                        })
                        ->first();

                    $final_bogo_status = false;
                    $final_bogo_label = '';
                    
                    if ($list_type == 4 && $bogo && (
                        ($bogo->product_id && $bogo->product_id == $data->productId) ||
                        ($bogo->brand_id && $bogo->brand_id == $data->brand_id) ||
                        ($bogo->subcategory_id && $bogo->subcategory_id == $data->subcategory_id) ||
                        ($bogo->category_id && $bogo->category_id == $data->category_id)
                    )) {
                        $final_bogo_status = true;
                        $final_bogo_label = 'BOGO';
                    } elseif ($list_type != 4 && $bogo) {
                        $final_bogo_status = true;
                        $final_bogo_label = 'BOGO';
                    }

                    // -----Offer-----
                    $offer = Offers::where('status', 1)
                    ->whereDate('expiry_date', '>=', $today)
                    ->where(function ($query) use ($data) {
                        $query->where('product_id', $data->productId)
                            ->orWhere(function ($q) use ($data) {
                                $q->whereNotNull('subcategory_id')
                                    ->where('subcategory_id', $data->subcategory_id);
                            })
                            ->orWhere(function ($q) use ($data) {
                                $q->whereNull('subcategory_id')
                                    ->where('category_id', $data->category_id);
                            });

                        if (!empty($data->brand_id)) {
                            $query->orWhere('brand_id', $data->brand_id);
                        }
                    })
                    ->first();

                    $final_offer_status = false;
                    $final_offer_label = '';
                    $final_offer_type = null;
                    $final_discount_amount = 0;
                    $final_offer_category_id = $data->category_id;
                    $final_offer_subcategory_id = $data->subcategory_id;
                    $final_offer_brand_id = $data->brand_id;

                    if ($list_type == 4 && $offer && (
                        ($offer->product_id && $offer->product_id == $data->productId) ||
                        ($offer->brand_id && $offer->brand_id == $data->brand_id) ||
                        ($offer->subcategory_id && $offer->subcategory_id == $data->subcategory_id) ||
                        ($offer->category_id && $offer->category_id == $data->category_id)
                    )) {
                        $final_offer_status = true;
                        $final_offer_type = $offer->offer_type ?? null;
                        $final_discount_amount = $offer->dis_amount ?? 0;
                    } elseif ($list_type != 4 && $offer) {
                        $final_offer_status = true;
                        $final_offer_type = $offer->offer_type ?? null;
                        $final_discount_amount = $offer->dis_amount ?? 0;
                    }

                    if ($final_offer_status) {
                        $formatted_discount = rtrim(rtrim(number_format($final_discount_amount, 2, '.', ''), '0'), '.');

                        if ($final_offer_type == 'flat') {
                            $final_offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
                        } elseif ($final_offer_type == 'percentage') {
                            $final_offer_label = $formatted_discount . '% off';
                            $final_discount_amount = max(0, $original_price * $formatted_discount / 100);
                        }
                    }

                    $productArr = [
                        'product_id'=>strval(@$data->productId),
                        'product_title'=>strval(@$title),
                        'product_rating'=>strval(@$data->average_rating),
                        'product_size' => strval(@$product_size),
                        'product_image'=> $image_path,
                        'product_orignal_price'=> $original_price,
                        'product_discounted_price'=>$discounted_price,
                        // 'product_discounted_price'=>strval((int)$discounted_price),
                        'is_favourite' => $is_favourite,
                        'inCart' => $inCart,
                        'variant_id'=>strval(@$data->product_variant_id),
                        'offer_status'=>(bool) $final_offer_status,
                        'offer_type'=>$final_offer_type,
                        'discount_amount'=>strval($final_discount_amount),
                        'offer_label'=> $final_offer_label,
                        'bogo_status'=>(bool) $final_bogo_status,
                        'bogo_label'=>$final_bogo_label,
                        'product_category_id'=>$final_offer_category_id,
                        'product_subcategory_id'=>$final_offer_subcategory_id,
                        'product_brand_id'=>$final_offer_brand_id,
                    ];

                    $productDataArr[] = $productArr;
                }        

                $result['code']     =    strval(1);
                $result['message']  =   'success';
                $result['result']  =   $productDataArr; 
                $result['total_product_count']  =  $total_product_count;
            }else{
                $result['code']     =   strval(0);
                $result['message']  =   'no_data_found';
                $result['result']   =   [];
            }
        
            $mainResult = $result;   
        
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
    }

    //new
    public function getRealtedProduct(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
            'subcategory_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }
        
        $page = $request->page;
        $uniqid = $request->uniqid;
        if($page==''){
            $page = 1;
        }
        $product_subcategoryid = $request->subcategory_id;
        $products = Product::activeProductsBasedOnRelations()->where('product.subcategory_id',$product_subcategoryid)->where('product.id',"!=",$request->product_id);
        // $products = $products->paginate(6, ['*'], 'page', $page);
        $products = $products->take(6)->get();

        // Special offer check
        $today = Carbon::now()->toDateString();

        if($products)
        {
            foreach ($products as $related) {
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
       

        $result = [];
        if($products){
            $productDataArr = [];
            $is_favourite = 0;
            foreach($products as $data){
                if ($request->language == 1) {
                    $title = ($data->product_name_fr) ? $data->product_name_fr : $data->product_name;
                } else {
                    $title = $data->product_name ?? '';
                } 
                $product_image = $data->get_product_images->first();
                $product_variant = $data->get_product_variants->first();
                $product_unit = Helper::getUnitById($product_variant->variant_uof);
                $product_size =  ($product_variant->variant_size) ? $product_variant->variant_size . ' ' . $product_unit : '' ;
                if(file_exists(public_path() . '/uploads/product/' . $product_image->image)){
                    $image_path =  asset('uploads/product/' . $product_image->image);
                }else{
                    $image_path =  asset('assets/frontend/images/image-not-avilable.png');
                }
                // if(!empty($user_id)){
                //     $favourite = FavoriteProduct::where('user_id', $user_id)->where('product_id', $data->id)->where('status', 1)->count();                  
                //     if($favourite ==1){
                //         $is_favourite = 1;
                //     }
                // }
                $is_favourite = 0;   
                if(!empty($uniqid)){
                    $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                   
                    if($userData!=""){
                        $favourite = FavoriteProduct::where('user_id', $userData->id)->where('product_id', $data->id)->where('status', 1)->count();  
                                     
                        if($favourite ==1){
                            $is_favourite = 1;
                        }
                    }                    
                }

                // Checking already present in cart
                $inCart=0;
                if(!empty($uniqid)){
                    $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                   
                    if($userData!=""){
                        $cartItem = DB::table('cart')->where('user_id',$userData->id)->where('product_id', $data->id)->where('status', 1)->first();
                        if($cartItem){
                            $inCart = 1;
                        }
                    }                    
                }


                $original_price = ($product_variant->variant_price) ? $product_variant->variant_price :'';

                $discounted_price = ($product_variant->variant_discounted_price=='' && $product_variant->variant_discounted_price==0)? $product_variant->variant_discounted_price :NULL;

                // offer and Bogo status 
                    $offer_status = $data->offer_status ? true: false;
                    $discount_amount = $data->discount_amount ? $data->discount_amount: 0;
                    $offer_type =  $data->offer_type ? $data->offer_type: null;
                    $bogo_status=$data->bogo_status ? true: false;
                    $offer_label = '';
                    $bogo_label = '';

                    if($offer_status)
                    {
                        $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

                        if ($offer_type == 'flat') {
                            $offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
                        } elseif ($offer_type == 'percentage') {
                            $offer_label = $formatted_discount  . '% off';
                            $discount_amount= max(0, $original_price * $formatted_discount / 100);
                        }
                    }

                    if($bogo_status)
                    {
                        $bogo_label = 'BOGO';
                    }         

                $productDataArr[] = [
                    'product_id'=>strval(@$data->id),
                    'product_title'=>strval(@$title),
                    'product_rating'=>strval(@$data->average_rating),
                    'product_size' => strval(@$product_size),
                    'product_image'=> $image_path,
                    'product_orignal_price'=> $original_price,
                    'product_discounted_price'=>$discounted_price,
                    'is_favourite' => $is_favourite,
                    'inCart' => $inCart,
                    'varinat_id' => $product_variant->id,
                    'offer_status' => $offer_status,
                    'discount_amount' => strval($discount_amount),
                    'offer_type' => $offer_type,
                    'offer_label' => $offer_label,
                    'bogo_status' => $bogo_status,
                    'bogo_label' => $bogo_label,
                ];
            }
            $result['code']     =    strval(1);
            $result['message']  =   'success';
            $result['result']   =   $productDataArr; 
        }else{
            $result['code']     =   strval(0);
            $result['message']  =   'no_data_found';
            $result['result']   =   [];
        }
    
        $mainResult = $result;       
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
    }  
    //new
    public function getCommonProducts(Request $request)
    {   
        $validator = \Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }      

        $uniqid = $request->uniqid;
        if($request->type ==1)
        {
            //1 for recent view
            $validator = \Validator::make($request->all(), [
                'product_ids' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => strval(0),
                    'error'=>$validator->messages(),
                    'data' => null
                ], 200);
            }
           
            $product_ids = explode(',', $request->product_ids);
            if(!empty($product_ids)){
                $product_ids = array_reverse($product_ids); 
            }
            
            $products = Product::activeProductsBasedOnRelations()->whereIn('product.id',$product_ids)->orderByRaw(DB::raw("FIELD(id, " . implode(',', $product_ids ).")"));
           
        }else if($request->type ==2){
            $subCatgyIds = MostViewProduct::getMostViewProductSubcategoryIds();
            $products = Product::activeProductsBasedOnRelations()->whereIn('product.subcategory_id',$subCatgyIds);
        }
        else if($request->type ==3){            
            $top_product_count = Order::getTopSellingProduct();
            $product_id = array();
            foreach($top_product_count as $result ){
                $product_id[] = $result->product_id;
            }
            $products = Product::activeProductsBasedOnRelations()->whereIn('product.id',$product_id);
        }

        $page = $request->page;
        if($page==''){
            $page = 1;
        }
        // $products = $products->paginate(6, ['*'], 'page', $page);
        $products = $products->take(6)->get();

        // Special offer check
        // $today = Carbon::now()->toDateString();

        // if($products)
        //     {
        //     foreach ($products as $common) {
        //             $bogo = Bogo::where('status', 1)
        //                 ->whereDate('start_date', '<=', $today)
        //                 ->whereDate('end_date', '>=', $today)
        //                 ->where(function ($query) use ($common) {
        //                     $query->where('product_id', $common->id)
        //                         ->orWhere(function ($q) use ($common) {
        //                             $q->whereNotNull('subcategory_id')
        //                                 ->where('subcategory_id', $common->subcategory_id);
        //                         })
        //                         ->orWhere(function ($q) use ($common) {
        //                             $q->whereNull('subcategory_id')
        //                                 ->where('category_id', $common->category_id);
        //                         });

        //                     if (!empty($common->brand_id)) {
        //                         $query->orWhere('brand_id', $common->brand_id);
        //                     }
        //                 })
        //                 ->first();

        //             $common->bogo_status = $bogo ? true : false; 


        //             // offer check
        //             $offer = Offers::where('status', 1)
        //                 ->whereDate('expiry_date', '>=', $today)
        //                 ->where(function ($query) use ($common) {
        //             $query->where('product_id', $common->id)
        //                 ->orWhere(function ($q) use ($common) {
        //                     $q->whereNotNull('subcategory_id')
        //                         ->where('subcategory_id', $common->subcategory_id);
        //                 })
        //                 ->orWhere(function ($q) use ($common) {
        //                     $q->whereNull('subcategory_id')
        //                         ->where('category_id', $common->category_id);
        //                 });

        //             if (!empty($common->brand_id)) {
        //                 $query->orWhere('brand_id', $common->brand_id);
        //             }
        //         })
        //         ->first();

        //         if ($offer) {
        //             $common->offer_status = true;
        //             $common->discount_amount = $offer->dis_amount;
        //             $common->offer_type = $offer->offer_type;
        //         } else {
        //             $common->offer_status = false;
        //             $common->discount_amount = 0;
        //             $common->offer_type = null;
        //         }
        //     }
        // }
 
        $result = [];
        if($products){
            $productDataArr = [];
            $is_favourite = 0;
            foreach($products as $data){
                if ($request->language == 1) {
                    $title = ($data->product_name_fr) ? $data->product_name_fr : $data->product_name;
                } else {
                    $title = $data->product_name ?? '';
                } 
                $product_image = $data->get_product_images->first();
                $product_variant = $data->get_product_variants->first();
                $product_unit = Helper::getUnitById($product_variant->variant_uof);
                $product_size =  ($product_variant->variant_size) ? $product_variant->variant_size . ' ' . $product_unit : '' ;
                if(file_exists(public_path() . '/uploads/product/' . $product_image->image)){
                    $image_path =  asset('uploads/product/' . $product_image->image);
                }else{
                    $image_path =  asset('assets/frontend/images/image-not-avilable.png');
                }
                // if(!empty($user_id)){
                //     $favourite = FavoriteProduct::where('user_id', $user_id)->where('product_id', $data->id)->where('status', 1)->count();                  
                //     if($favourite ==1){
                //         $is_favourite = 1;
                //     }
                // }

                $is_favourite = 0;   
                if(!empty($uniqid)){                   
                    $userData = DB::table('main_users')->where('uniqid',$uniqid)->first();                   
                    if($userData!=""){
                        $favourite = FavoriteProduct::where('user_id', $userData->id)->where('product_id', $data->id)->where('status', 1)->count();                        
                                     
                        if($favourite ==1){
                            $is_favourite = 1;
                        }
                    }                    
                }

                // Checking already present in cart
                $inCart=0;
                if(!empty($uniqid)){
                    $userData = DB::table('main_users')->where('uniqid',$uniqid)->first();     

                    if($userData!=""){
                      $cartItem = DB::table('cart')->where('user_id',$userData->id)->where('product_id', $data->id)->where('status', 1)->first();

                        if($cartItem){
                            $inCart = 1;
                        }
                    }
                }


                $original_price = ($product_variant->variant_price) ? $product_variant->variant_price :'';

                $discounted_price = ($product_variant->variant_discounted_price=='' && $product_variant->variant_discounted_price==0)? $product_variant->variant_discounted_price :NULL;


                   // === BOGO Check ===
                $today = Carbon::now()->toDateString();
                $bogo = Bogo::where('status', 1)
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->where(function ($query) use ($data) {
                        $query->where('product_id', $data->id)
                            ->orWhere(function ($q) use ($data) {
                                $q->whereNotNull('subcategory_id')
                                    ->where('subcategory_id', $data->subcategory_id);
                            })
                            ->orWhere(function ($q) use ($data) {
                                $q->whereNull('subcategory_id')
                                    ->where('category_id', $data->category_id);
                            });

                        if (!empty($data->brand_id)) {
                            $query->orWhere('brand_id', $data->brand_id);
                        }
                    })
                    ->first();
                $bogo_status = $bogo ? true : false;
                $bogo_label = $bogo_status ? 'BOGO' : '';

                // === Offer Check ===
                $offer = Offers::where('status', 1)
                    ->whereDate('expiry_date', '>=', $today)
                    ->where(function ($query) use ($data) {
                        $query->where('product_id', $data->id)
                            ->orWhere(function ($q) use ($data) {
                                $q->whereNotNull('subcategory_id')
                                    ->where('subcategory_id', $data->subcategory_id);
                            })
                            ->orWhere(function ($q) use ($data) {
                                $q->whereNull('subcategory_id')
                                    ->where('category_id', $data->category_id);
                            });

                        if (!empty($data->brand_id)) {
                            $query->orWhere('brand_id', $data->brand_id);
                        }
                    })
                    ->first();

                // offer and Bogo status 
                $offer_status = $offer ? true : false;
                $discount_amount = $offer ? $offer->dis_amount : 0;
                $offer_type = $offer ? $offer->offer_type : null;
                $offer_label = '';

                if($offer_status)
                {
                    $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

                    if ($offer_type == 'flat') {
                        $offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
                    } elseif ($offer_type == 'percentage') {
                        $offer_label = $formatted_discount  . '% off';
                        $discount_amount= max(0, $original_price * $formatted_discount / 100);
                    }
                }

                $productDataArr[] = [
                    'product_id'=>strval(@$data->id),
                    'product_title'=>strval(@$title),
                    'product_rating'=>strval(@$data->average_rating),
                    'product_size' => strval(@$product_size),
                    'product_image'=> $image_path,
                    'product_orignal_price'=> $original_price,
                    'product_discounted_price'=>$discounted_price,
                    'is_favourite' => $is_favourite,
                    'inCart' => $inCart,
                    'variant_id'=>strval(@$product_variant->id),
                    'offer_status' => $offer_status,
                    'discount_amount' => strval($discount_amount),
                    'offer_type' => $offer_type,
                    'offer_label' => $offer_label,
                    'bogo_status' => $bogo_status,
                    'bogo_label' => $bogo_label,

                ];
            }
            $result['code']     =    strval(1);
            $result['message']  =   'success';
            $result['result']   =   $productDataArr; 
        }else{
            $result['code']     =   strval(0);
            $result['message']  =   'no_data_found';
            $result['result']   =   [];
        }
    
        $mainResult = $result;       
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
    }  
    //new
    public function getRecentlyProducts(Request $request)
    {   
        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
            'subcategory_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $page = $request->page;
        if($page==''){
            $page = 1;
        }

        
        // $products = $products->paginate(6, ['*'], 'page', $page);
        $products = $products->take(6)->get();


         // Special offer check
        $today = Carbon::now()->toDateString();

        if($products)
        {
            foreach ($products as $recent) {
                    $bogo = Bogo::where('status', 1)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->where(function ($query) use ($recent) {
                            $query->where('product_id', $recent->id)
                                ->orWhere(function ($q) use ($recent) {
                                    $q->whereNotNull('subcategory_id')
                                        ->where('subcategory_id', $recent->subcategory_id);
                                })
                                ->orWhere(function ($q) use ($recent) {
                                    $q->whereNull('subcategory_id')
                                        ->where('category_id', $recent->category_id);
                                });

                            if (!empty($recent->brand_id)) {
                                $query->orWhere('brand_id', $recent->brand_id);
                            }
                        })
                        ->first();

                    $recent->bogo_status = $bogo ? true : false; 


                    // offer check
                    $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($recent) {
                    $query->where('product_id', $recent->id)
                        ->orWhere(function ($q) use ($recent) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $recent->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($recent) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $recent->category_id);
                        });

                    if (!empty($recent->brand_id)) {
                        $query->orWhere('brand_id', $recent->brand_id);
                    }
                })
                ->first();

                if ($offer) {
                    $recent->offer_status = true;
                    $recent->discount_amount = $offer->dis_amount;
                    $recent->offer_type = $offer->offer_type;
                } else {
                    $recent->offer_status = false;
                    $recent->discount_amount = 0;
                    $recent->offer_type = null;
                }
            }
        }

        $uniqid = $request->uniqid;
        $result = [];
        if($products){
            $productDataArr = [];
            $is_favourite = 0;
            foreach($products as $data){
                if ($request->language == 1) {
                    $title = ($data->product_name_fr) ? $data->product_name_fr : $data->product_name;
                } else {
                    $title = $data->product_name ?? '';
                } 
                $product_image = $data->get_product_images->first();
                $product_variant = $data->get_product_variants->first();
                $product_unit = Helper::getUnitById($product_variant->variant_uof);
                $product_size =  ($product_variant->variant_size) ? $product_variant->variant_size . ' ' . $product_unit : '' ;
                if(file_exists(public_path() . '/uploads/product/' . $product_image->image)){
                    $image_path =  asset('uploads/product/' . $product_image->image);
                }else{
                    $image_path =  asset('assets/frontend/images/image-not-avilable.png');
                }
                // if(!empty($user_id)){
                //     $favourite = FavoriteProduct::where('user_id', $user_id)->where('product_id', $data->id)->where('status', 1)->count();                  
                //     if($favourite ==1){
                //         $is_favourite = 1;
                //     }
                // }
                $is_favourite = 0;   
                if(!empty($uniqid)){
                    $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                   
                    if($userData!=""){
                        $favourite = FavoriteProduct::where('user_id', $userData->id)->where('product_id', $data->id)->where('status', 1)->count();  
                                     
                        if($favourite ==1){
                            $is_favourite = 1;
                        }
                    }                    
                }

                // Checking already present in cart
                $inCart=0;  
                if(!empty($uniqid)){
                    $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                   
                    if($userData!=""){
                        $cartItem = DB::table('cart')->where('user_id',$userData->id)->where('product_id', $data->id)->where('status', 1)->first();
                        if($cartItem){
                            $inCart = 1;
                        }
                    }                    
                }


                $original_price = ($product_variant->variant_price) ? $product_variant->variant_price :'';

                $discounted_price = ($product_variant->variant_discounted_price=='' && $product_variant->variant_discounted_price==0)? $product_variant->variant_discounted_price :NULL;

                // offer and Bogo status 
                $offer_status = $data->offer_status ? true: false;
                $discount_amount = $data->discount_amount ? $data->discount_amount: 0;
                $offer_type =  $data->offer_type ? $data->offer_type: null;
                $bogo_status=$data->bogo_status ? true: false;
                $offer_label = '';
                $bogo_label = '';

                if($offer_status)
                {
                    $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

                    if ($offer_type == 'flat') {
                        $offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
                    } elseif ($offer_type == 'percentage') {
                        $offer_label = $formatted_discount  . '% off';
                    }
                }

                if($bogo_status)
                {
                    $bogo_label = 'BOGO';
                } 

                $productDataArr[] = [
                    'product_id'=>strval(@$data->id),
                    'product_title'=>strval(@$title),
                    'product_rating'=>strval(@$data->average_rating),
                    'product_size' => strval(@$product_size),
                    'product_image'=> $image_path,
                    'product_orignal_price'=> $original_price,
                    'product_discounted_price'=>$discounted_price,
                    'is_favourite' => $is_favourite,
                    'inCart' => $inCart,
                    'offer_status' => $offer_status,
                    'discount_amount' => strval($discount_amount),
                    'offer_type' => $offer_type,
                    'offer_label' => $offer_label,
                    'bogo_status' => $bogo_status,
                    'bogo_label' => $bogo_label,
                ];
            }
            $result['code']     =    strval(1);
            $result['message']  =   'success';
            $result['result']   =   $productDataArr; 
        }else{
            $result['code']     =   strval(0);
            $result['message']  =   'no_data_found';
            $result['result']   =   [];
        }
    
        $mainResult = $result;       
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
    }  
    //new
    public function productDetail(Request $request)
    {
        $result = [];
        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        if($request->all())
        {
            $product_id = $request->product_id;                
            $product_info = Product::activeProductsBasedOnRelations()->where('product.id',$product_id)->first();

            if (empty($product_info)) {
                $result['code']     =  strval(0);
                $result['message']  =   'no_data_found';
                $result['result']       =   [];
                
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }

            if ($request->language == 1) {
                $title = ($product_info->product_name_fr) ? $product_info->product_name_fr : $product_info->product_name;
                $short_desp = ($product_info->short_description_fr) ? $product_info->short_description_fr : $product_info->short_description;
                $long_descp = ($product_info->page_content_fr) ? $product_info->page_content_fr : $product_info->description;
                $category_title = ($product_info->get_category->title_fr) ? $product_info->get_category->title_fr :$product_info->get_category->title;
                $brand_title = ($product_info->get_brand_details->title_fr) ? $product_info->get_brand_details->title_fr :$product_info->get_brand_details->title;
            } else {
                $title = $product_info->product_name ?? '';
                $short_desp = $product_info->short_description??'';
                $long_descp = $product_info->description??'';
                $category_title = $product_info->get_category->title??'';
                $brand_title = $product_info->get_brand_details->title??'';
            } 
            $product_images = array();
            foreach($product_info->get_product_images as $result_image){
                if(file_exists(public_path() . '/uploads/product/' . $result_image->image)){
                    $product_images[] =  asset('uploads/product/' . $result_image->image);
                }else{
                    $product_images[] =  asset('assets/frontend/images/image-not-avilable.png');
                }
            }

            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $is_favourite = 0;     
            if($userData!=""){
                $favourite = FavoriteProduct::where('user_id', $userData->id)->where('product_id', $product_info->id)->where('status', 1)->count();                           
                if($favourite ==1){
                    $is_favourite = 1;
                }
            }  

            // Checking already present in cart
            $inCart=0;
            if($userData!=""){
                $cartItem = DB::table('cart')->where('user_id',$userData->id)->where('product_id', $product_info->id)->where('status', 1)->first();
                if($cartItem){
                    $inCart = 1;
                }
            }

            // Offer and Bogo Status
            $today = Carbon::now()->toDateString();

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

            // $offerDetails=[];
            $offerDetails = (object)[];
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
                $offer_label = '';
                $formatted_discount = rtrim(rtrim(number_format($offer->dis_amount, 2, '.', ''), '0'), '.');
                if ($offer->offer_type == 'flat') {
                    $offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
                } elseif ($offer->offer_type == 'percentage') {
                    $offer_label = $formatted_discount  . '% off';
                }

                $offerDetails = (object)[
                    'offer_type' => $offer->offer_type,
                    'discount_amount' => $offer->dis_amount,
                    'offer_label' => $offer_label,
                    'offer_status'=> $offer ? true : false
                ];
            } 

            // Add BOGO info into offerDetails
            $offerDetails->bogoStatus = $bogoStatus;
            $offerDetails->bogo_label = $bogoStatus ? 'BOGO' : '';

            $productDataArr = [
                'product_id'=>strval(@$product_info->id),
                'product_title'=>strval(@$title),
                'product_short_description'=>strval(@$short_desp),
                'product_long_description'=>strval(urldecode(@$long_descp)),
                'product_rating'=>strval(@$product_info->average_rating),
                'product_images'=> $product_images,
                'brand_title'=>strval(@$brand_title),
                'category_title'=>strval(@$category_title),
                'subcategory_id'=>strval(@$product_info->get_subcategory->id),
                'is_favourite' => $is_favourite,
                'inCart' => $inCart,
                'offerDetails'=> $offerDetails,
            ];
          
            $result['code']     =    strval(1);
            $result['message']  =   'success';
            $result['result']   =   $productDataArr; 

            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }else{

                $result['code']     =  strval(0);
                $result['message']  =   'something_went_wrong';
                $result['result']       =   [];
                
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }
    //New
    public function getProductVariant(Request $request){
        $result = [];
        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        if($request->all())
        {
            $product_id = $request->product_id;                
            $variant_info = ProductVariants::where('product_id', $product_id)->get();
            
            if (empty($variant_info)) {
                $result['code']     =  strval(0);
                $result['message']  =   'no_data_found';
                $result['result']       =   [];
                
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }

            $variant_arr = [];
            $today = Carbon::now()->toDateString();
            foreach($variant_info as $variant){
                $product_info = Product::activeProductsBasedOnRelations()->where('product.id',$variant->product_id)->first();
                $product_subcategory_id = $product_info->subcategory_id;
                $product_category_id = $product_info->category_id;
                $product_brand_id = $product_info->brand_id;

                // -----Offer-----
                $offer = Offers::where('status', 1)
                    ->whereDate('expiry_date', '>=', $today)
                    ->where(function ($query) use ($variant, $product_subcategory_id, $product_category_id, $product_brand_id) {
                        $query->where('product_id', $variant->product_id)
                            ->orWhere(function ($q) use ($product_subcategory_id) {
                                $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $product_subcategory_id);
                            })
                            ->orWhere(function ($q) use ($product_category_id) {
                                $q->whereNull('subcategory_id')
                                ->where('category_id', $product_category_id);
                            });

                        if (!empty($product_brand_id)) {
                            $query->orWhere('brand_id', $product_brand_id);
                        }
                    })
                    ->first();

                $discounted_price=0;
                if($offer)
                {
                    $offer_status = true;
                    $offer_type = $offer->offer_type ?? null;
                    $discount_amount = $offer->dis_amount ?? 0;

                    if ($offer_type == 'flat') {
                        $discounted_price = max(0, $variant->variant_price - $discount_amount);
                    } elseif ($offer_type == 'percentage') {
                        $discounted_price = max(0, $variant->variant_price - ($variant->variant_price * $discount_amount / 100));
                    }
                }


                $variant_arr[] = [
                    'variant_id'=>strval(@$variant->id),
                    'variant_qty'=>strval(@$variant->available_qty),
                    'variant_size'=>strval(@$variant->variant_size),
                    'variant_unit'=>strval(@$variant->variant_uof),
                    'variant_price'=>strval(@$variant->variant_price),
                    // 'variant_discount_price'=> strval(@$variant->variant_discounted_price),
                    'variant_discount_price'=> strval((int)$discounted_price)
                ];
            }          
            $result['code']     =    strval(1);
            $result['message']  =   'success';
            $result['result']   =   $variant_arr; 

            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }else{
            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']   =   [];            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    // public function productsfiltter(Request $request){
    //     $result = [];
    //     $finalArr = [];
    
    //     if($request->all())
    //     {
    //         $categoryData  = Categories::with(['subcategory'=>function ($query){
    //             $query->where('status',1);
    //         }])->where('status',1)->get();
    //         $brandData = Brand::where('status',1)->orderBy('title','asc')->get();
         

    //         $mainArr = [];
    //         if (!empty($categoryData)) {
    //             $categoryArr = [];
    //             foreach ($categoryData as $category) {                    
    //                 if ($request->language == 1) {
    //                     $category_title = $category->title_fr ?? $category->title;
    //                 } else {
    //                     $category_title = $category->title ?? '';
    //                 }    
                    
    //                 $subcategory = [];
    //                 if(!empty($category->subcategory)){
    //                     foreach($category->subcategory as $sub_category){
    //                         if ($request->language == 1) {
    //                             $title = $sub_category->title_fr ?? $sub_category->title;
    //                         } else {
    //                             $title = $sub_category->title ?? '';
    //                         } 
    //                         $subcategory[] = [
    //                             'id'=>strval(@$sub_category->id),
    //                             'title'=>strval(@$title)
    //                         ];
    //                     }
    //                 }
                    
    //                 $categoryArr['category_list'][] = [
    //                     'id'=>strval(@$category->id),
    //                     'title'=>strval(@$category_title),
    //                     'subcategory_list'=> $subcategory
    //                 ];
    //             }

    //             if (!empty($brandData)) {
    //                 $brandArr = [];
    //                 foreach ($brandData as $brands) {                    
    //                     if ($request->language == 1) {
    //                         $title = $brands->title_fr ?? $brands->title;
    //                     } else {
    //                         $title = $brands->title ?? '';
    //                     }

    //                     $brandArr['brand_list'][] = [
    //                         'id'=>strval(@$brands->id),
    //                         'title'=>strval(@$title),
    //                     ];
    //                 }                    
    //             }
               
    //             $product_val =  new Product;
    //             $product_price = [];
    //             $product_max_price = $product_val->getproductMaxPrice();
    //             $product_min_price = $product_val->getproductMinPrice();
    //             $product_price['min_price'] = "0";
    //             if (!empty($product_max_price)) {
    //                 $product_price['max_price'] = $product_max_price->max_price;
    //             }               
    //             if (!empty($product_min_price->min_price)) {
    //                 $product_price['min_price'] = $product_min_price->min_price;
    //             }

    //             $mainArr = array_merge($categoryArr,$brandArr,$product_price);
    //         }
            
    //         $result['code']     =  strval(1);
    //         $result['message']  =  'success';
    //         $result['result']   = $mainArr;
    //         $mainResult   =   $result;
    //         return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

    //     }else{
    //         $result['code']     =  strval(0);
    //         $result['message']  =   'something_went_wrong';
    //         $result['result']       =   [];
    //         $mainResult   =   $result;
    //         return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     }
    // }

        public function productsfiltter(Request $request){
        $result = [];
        $finalArr = [];

        $category_id = $request->cid;
        $subcategory_id = $request->sid;
        $brandDataId=$request->bid;
    
        if($request->all())
        {

            $categoryData = Categories::with(['subcategory' => function ($query) use ($subcategory_id) {
            $query->where('status', 1);
            if (!empty($subcategory_id)) {
                $query->where('id', $subcategory_id);
            }
            }])
            ->where('id', $category_id)
            ->where('status', 1)
            ->get();

            $product_val =  new Product;
            $product_price = [];
            $product_max_price = $product_val->getproductMaxPrice();
            $product_min_price = $product_val->getproductMinPrice();
            $product_price['min_price'] = "0";
            if (!empty($product_max_price)) {
                $product_price['max_price'] = $product_max_price->max_price;
            }               
            if (!empty($product_min_price->min_price)) {
                $product_price['min_price'] = $product_min_price->min_price;
            }
            
            $products =  DB::table('product as p')
                        ->select('p.id as productId','p.average_rating','p.product_name','p.product_name_fr','pi.image', 'pv.*', 'pv.final_price','mvp.count','p.offer','p.is_product_bestseller','p.category_id','p.subcategory_id','p.brand_id')
                        ->join('product_image as pi', 'p.id', '=', 'pi.product_id')
                        ->where('pi.status', '=', 1)
                        ->join('categories as c', 'p.category_id', '=', 'c.id')                    
                            ->where('c.status', '=', 1)
                        ->join('sub_categories as sc', 'p.subcategory_id', '=', 'sc.id')
                            ->where('sc.status', '=', 1)
                        ->join('brand as b', 'p.brand_id', '=', 'b.id')
                            ->where('b.status', '=', 1)
                        ->join(
                            DB::raw('(SELECT *,id as product_variant_id, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
                            function ($join) {
                                $join->on('p.id', '=', 'pv.product_id');
                            }
                        )
                        ->leftjoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
                        ->where('p.status', '=', 1)
                        ->groupBy('p.id');

                        if($category_id && $category_id!=null){
                        $products->where('c.id', '=', $category_id);
                        }

                        if($subcategory_id && $subcategory_id!=null){
                            $products->where('sc.id', '=', $subcategory_id);
                        }

                        if($brandDataId && $brandDataId!=null){
                            $products->where('p.brand_id', '=', $brandDataId);
                        }

                        $products  = $products->orderBy('p.id', 'DESC')->get();  

                        $brandIds = $products->pluck('brand_id')->unique()->filter()->toArray();

                        $brandData = Brand::where('status', 1)
                        ->whereIn('id', $brandIds)
                        ->orderBy('title', 'asc')
                        ->get();

                        if (!$products->isEmpty()) {
                            $max_price_value = $products->pluck('variant_price')->filter()->max();
                            $product_price['max_price'] = number_format((float) $max_price_value, 2, '.', '');
                        }

                        if($brandDataId)
                        {
                            $categoryIds = $products->pluck('category_id')->unique()->filter()->toArray();

                            $categoryData = Categories::with(['subcategory'=>function ($query){
                                $query->where('status',1);
                            }])
                            ->where('id', $categoryIds)
                            ->where('status',1)->get();
                        }
         

            $mainArr = [];
            if (!empty($categoryData)) {
                $categoryArr = [];
                foreach ($categoryData as $category) {                    
                    if ($request->language == 1) {
                        $category_title = $category->title_fr ?? $category->title;
                    } else {
                        $category_title = $category->title ?? '';
                    }    
                    
                    $subcategory = [];
                    if(!empty($category->subcategory)){
                        foreach($category->subcategory as $sub_category){
                            if ($request->language == 1) {
                                $title = $sub_category->title_fr ?? $sub_category->title;
                            } else {
                                $title = $sub_category->title ?? '';
                            } 
                            $subcategory[] = [
                                'id'=>strval(@$sub_category->id),
                                'title'=>strval(@$title)
                            ];
                        }
                    }
                    
                    $categoryArr['category_list'][] = [
                        'id'=>strval(@$category->id),
                        'title'=>strval(@$category_title),
                        'subcategory_list'=> $subcategory
                    ];
                }

                if (!empty($brandData)) {
                    $brandArr = [];
                    foreach ($brandData as $brands) {                    
                        if ($request->language == 1) {
                            $title = $brands->title_fr ?? $brands->title;
                        } else {
                            $title = $brands->title ?? '';
                        }

                        $brandArr['brand_list'][] = [
                            'id'=>strval(@$brands->id),
                            'title'=>strval(@$title),
                        ];
                    }                    
                }
               

                $mainArr = array_merge($categoryArr,$brandArr,$product_price);
            }
            
            $result['code']     =  strval(1);
            $result['message']  =  'success';
            $result['result']   = $mainArr;
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

        }else{
            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function addMostViewProduct(Request $request){
        $result = [];
        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(1),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }
        $product_id = $request->product_id;
        $mostViewProduct = MostViewProduct::where('product_id',$product_id)->first();
        if(!empty($mostViewProduct)){
            $mostViewProduct->count = (int) $mostViewProduct->count + 1;
            $mostViewProduct->save();
        }else{
            $mostViewProduct = new MostViewProduct();
            $mostViewProduct->product_id = $product_id;
            $mostViewProduct->count = 1;
            $mostViewProduct->status = 1;
            $mostViewProduct->save();
        }
        if($mostViewProduct){
            $result['code']     =  strval(1);
            $result['message']  =   'success';
            $result['result']   =   [];            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }else{
            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']   =   [];            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
             
    }

    public function addFavorite(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        if ($request->uniqid) {
            $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
            if ($response['code'] != 1) {
                $mainResult   =   $response;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }

        $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
        
        $productFav = DB::table('favorite_product')->where('product_id',$request->product_id)->where('user_id',$userData->id)->where('status',1)->first();
        
        if(empty($productFav))
        {
            if($post)
            {   
                //echo "<pre>";print_r($productFav);exit();
                if (empty($productFav)) {
                    $addFavorite = new FavoriteProduct();
                    $addFavorite->product_id = $request->product_id;
                    $addFavorite->user_id = @$userData->id;
                    $addFavorite->status = @$request->is_fav;
                    $addFavorite->save();
                    
                }else{
                    $editFavorite = FavoriteProduct::where('user_id', $userData->id)->where('product_id',$request->product_id)->update(array(
                        'status' => @$request->is_fav,
                    ));
                }

                $result['code']     =  strval(1);
                $result['message']  =   'add_to_favourite';
                //$result['result']   = [];
        
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
            else
            {
                $result['code']     =  strval(0);
                $result['message']  =   'something_went_wrong';
                $result['result']       =   [];
                
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }else{
            $result['code']     =  strval(1);
            $result['message']  =   'product_already_in_favorite';
            //$result['result']   =   [];
            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function deleteFavorite(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

        if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $editFavorite = FavoriteProduct::where('user_id', $userData->id)->where('product_id',$request->product_id)->update(array(
                            'status' => 2,
                        ));
                $result['code']     =  strval(1);
                $result['message']  =   'success';
                // $result['result']       = [];
        
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                
        }else{

                $result['code']     =  strval(0);
                $result['message']  =   'something_went_wrong';
                $result['result']       =   [];
                
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    // public function favoriteList(Request $request)
    // {   
    //      $validator = \Validator::make($request->all(), [
    //          'uniqid' => 'required',
    //          'token' => 'required',
    //      ]);
 
    //      if ($validator->fails()) {
    //          return response()->json([
    //              'status_code' => strval(0),
    //              'error'=>$validator->messages(),
    //              'data' => null
    //          ], 200);
    //      } 
 
    //     $userData = DB::table('main_users')->join('favorite_product','main_users.id','favorite_product.user_id')->select(DB::raw('group_concat(favorite_product.product_id) as product_ids') )->where('main_users.uniqid',$request->uniqid)->groupBy('main_users.id')->where('favorite_product.status',1)->first();
        
        
    //     if(!empty($userData->product_ids))
    //     {   
    //         $proudct_id = explode(',',$userData->product_ids);
    //         $products = Product::activeProductsBasedOnRelations()->whereIn('product.id',$proudct_id);
            
    //         $page = $request->page;
    //         if($page==''){
    //             $page = 1;
    //         }
    //         $products = $products->paginate(8, ['*'], 'page', $page);
            
    //         $result = [];
    //         if($products){
    //             $productDataArr = [];
    //             $is_favourite = 0;
    //             foreach($products as $data){
    //                 $is_favourite = 1;
    //                 if ($request->language == 1) {
    //                     $title = ($data->product_name_fr) ? $data->product_name_fr : $data->product_name;
    //                 } else {
    //                     $title = $data->product_name ?? '';
    //                 } 
    //                 $product_image = $data->get_product_images->first();
    //                 $product_variant = $data->get_product_variants->first();
    //                 $product_unit = Helper::getUnitById($product_variant->variant_uof);
    //                 $product_size =  ($product_variant->variant_size) ? $product_variant->variant_size . ' ' . $product_unit : '' ;
    //                 if(file_exists(public_path() . '/uploads/product/' . $product_image->image)){
    //                     $image_path =  asset('uploads/product/' . $product_image->image);
    //                 }else{
    //                     $image_path =  asset('assets/frontend/images/image-not-avilable.png');
    //                 }
    
    //                 $original_price = ($product_variant->variant_price) ? $product_variant->variant_price :'';
                    
    //                 $discounted_price = ($product_variant->variant_discounted_price == '' && $product_variant->variant_discounted_price == 0) ? $product_variant->variant_discounted_price : NULL;
                    
    //                 $productDataArr[] = [
    //                     'product_id'=>strval(@$data->id),
    //                     'product_title'=>strval(@$title),
    //                     'product_rating'=>strval(@$data->average_rating),
    //                     'product_size' => strval(@$product_size),
    //                     'product_image'=> $image_path,
    //                     'product_orignal_price'=> $original_price,
    //                     'product_discounted_price'=>$discounted_price,
    //                     'is_favourite' => $is_favourite,
    //                     'variant_id'=>strval(@$product_variant->id),
    //                 ];
    //             }
    //             $result['code']     =    strval(1);
    //             $result['message']  =   'success';
    //             $result['result']   =   $productDataArr; 
    //         }else{
    //             $result['code']     =   strval(0);
    //             $result['message']  =   'no_data_found';
    //             $result['result']   =   [];
    //         }
    //     }else{
    //             $result['code']     =   strval(-5);
    //             $result['message']  =   'no_data_found';
    //             // $result['result']   =   [];
    //     }    
    //     $mainResult = $result;       
    //     return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
    // }

    public function favoriteList(Request $request)
    {   
         $validator = \Validator::make($request->all(), [
             'uniqid' => 'required',
             'token' => 'required',
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                 'status_code' => strval(0),
                 'error'=>$validator->messages(),
                 'data' => null
             ], 200);
         } 
        $userData = DB::table('main_users')->join('favorite_product','main_users.id','favorite_product.user_id')->select(DB::raw('group_concat(favorite_product.product_id) as product_ids') )->where('main_users.uniqid',$request->uniqid)->groupBy('main_users.id')->where('favorite_product.status',1)->first();
        
        
        if(!empty($userData->product_ids))
        {   
            $proudct_id = explode(',',$userData->product_ids);
            $products = Product::activeProductsBasedOnRelations()->whereIn('product.id',$proudct_id);
            
            $page = $request->page;
            if($page==''){
                $page = 1;
            }
            $products = $products->paginate(8, ['*'], 'page', $page);
      
             // Special offer check
            $today = Carbon::now()->toDateString();

            if($products)
            {
                foreach ($products as $favour) {
                        $bogo = Bogo::where('status', 1)
                            ->whereDate('start_date', '<=', $today)
                            ->whereDate('end_date', '>=', $today)
                            ->where(function ($query) use ($favour) {
                                $query->where('product_id', $favour->id)
                                    ->orWhere(function ($q) use ($favour) {
                                        $q->whereNotNull('subcategory_id')
                                            ->where('subcategory_id', $favour->subcategory_id);
                                    })
                                    ->orWhere(function ($q) use ($favour) {
                                        $q->whereNull('subcategory_id')
                                            ->where('category_id', $favour->category_id);
                                    });

                                if (!empty($favour->brand_id)) {
                                    $query->orWhere('brand_id', $favour->brand_id);
                                }
                            })
                            ->first();

                        $favour->bogo_status = $bogo ? true : false; 


                        // offer check
                        $offer = Offers::where('status', 1)
                            ->whereDate('expiry_date', '>=', $today)
                            ->where(function ($query) use ($favour) {
                        $query->where('product_id', $favour->id)
                            ->orWhere(function ($q) use ($favour) {
                                $q->whereNotNull('subcategory_id')
                                    ->where('subcategory_id', $favour->subcategory_id);
                            })
                            ->orWhere(function ($q) use ($favour) {
                                $q->whereNull('subcategory_id')
                                    ->where('category_id', $favour->category_id);
                            });

                        if (!empty($favour->brand_id)) {
                            $query->orWhere('brand_id', $favour->brand_id);
                        }
                    })
                    ->first();

                    if ($offer) {
                        $favour->offer_status = true;
                        $favour->discount_amount = $offer->dis_amount;
                        $favour->offer_type = $offer->offer_type;
                    } else {
                        $favour->offer_status = false;
                        $favour->discount_amount = 0;
                        $favour->offer_type = null;
                    }
                }
            }

            
            $result = [];
            if($products){
                $productDataArr = [];
                $is_favourite = 0;
                foreach($products as $data){
                    $is_favourite = 1;

                    // Checking already present in cart
                    $inCart=0;

                    if($request->uniqid != ''){
                        $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();

                        if(!empty($userData)){
                            $cartItem = DB::table('cart')->where('user_id',$userData->id)->where('product_id', $data->id)->where('status', 1)->first();
                            if($cartItem){
                                $inCart = 1;
                            }
                        }
                    }

                    if ($request->language == 1) {
                        $title = ($data->product_name_fr) ? $data->product_name_fr : $data->product_name;
                    } else {
                        $title = $data->product_name ?? '';
                    } 
                    $product_image = $data->get_product_images->first();
                    $product_variant = $data->get_product_variants->first();
                    $product_unit = Helper::getUnitById($product_variant->variant_uof);
                    $product_size =  ($product_variant->variant_size) ? $product_variant->variant_size . ' ' . $product_unit : '' ;
                    if(file_exists(public_path() . '/uploads/product/' . $product_image->image)){
                        $image_path =  asset('uploads/product/' . $product_image->image);
                    }else{
                        $image_path =  asset('assets/frontend/images/image-not-avilable.png');
                    }
    
                    $original_price = ($product_variant->variant_price) ? $product_variant->variant_price :'';
                    
                    $discounted_price = ($product_variant->variant_discounted_price == '' && $product_variant->variant_discounted_price == 0) ? $product_variant->variant_discounted_price : NULL;
                    
                    // offer and Bogo status 
                    $offer_status = $data->offer_status ? true: false;
                    $discount_amount = $data->discount_amount ? $data->discount_amount: 0;
                    $offer_type =  $data->offer_type ? $data->offer_type: null;
                    $bogo_status=$data->bogo_status ? true: false;
                    $offer_label = '';
                    $bogo_label = '';

                    if($offer_status)
                    {
                        $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

                        if ($offer_type == 'flat') {
                            $offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
                        } elseif ($offer_type == 'percentage') {
                            $offer_label = $formatted_discount  . '% off';
                            $discount_amount= max(0, $original_price * $formatted_discount / 100);
                        }
                    }

                    if($bogo_status)
                    {
                        $bogo_label = 'BOGO';
                    }         
                    $productDataArr[] = [
                        'product_id'=>strval(@$data->id),
                        'product_title'=>strval(@$title),
                        'product_rating'=>strval(@$data->average_rating),
                        'product_size' => strval(@$product_size),
                        'product_image'=> $image_path,
                        'product_orignal_price'=> $original_price,
                        'product_discounted_price'=>$discounted_price,
                        'is_favourite' => $is_favourite,
                        'inCart' => $inCart,
                        'variant_id'=>strval(@$product_variant->id),
                        'offer_status' => $offer_status,
                        'discount_amount' => strval($discount_amount),
                        'offer_type' => $offer_type,
                        'offer_label' => $offer_label,
                        'bogo_status' => $bogo_status,
                        'bogo_label' => $bogo_label,
                    ];
                }
                $result['code']     =    strval(1);
                $result['message']  =   'success';
                $result['result']   =   $productDataArr; 
            }else{
                $result['code']     =   strval(0);
                $result['message']  =   'no_data_found';
                $result['result']   =   [];
            }
        }else{
                // $result['code']     =   strval(-5);
                $result['code']     =   strval(1);
                $result['message']  =   'no_data_found';
                $result['result']   =   [];
        }    
        $mainResult = $result;       
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
    }

    // reward Points section
    public function rewardPoint(Request $request)
    {   
         $validator = \Validator::make($request->all(), [
             'uniqid' => 'required',
             'token' => 'required',
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                 'status_code' => strval(0),
                 'error'=>$validator->messages(),
                 'data' => null
             ], 200);
         } 

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
        if ($response['code'] != 1) {
            $mainResult   =   $response;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
        
        $uniqid = $request->uniqid;
      
        $result = [];  
        if(!empty($uniqid))
        {   
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();

            if (!$userData) {
                return response()->json([
                    'status_code' => strval(0),
                    'error' => 'Invalid user.',
                    'data' => null
                ], 200);
            }

            $user_id = $userData->id;

            $points = LoyaltyPoints::where('user_id', $user_id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    // Credit points: order must be delivered
                    $q->where('type', 'credit')
                       ->where('status', 1)
                      ->whereIn('order_id', function($sub) {
                          $sub->select('order_id')
                              ->from('order')
                              ->where('order_status', 3);
                      });
                })->orWhere(function ($q) {
                    // Debit points: order must be delivered
                    $q->where('type', 'debit')
                       ->where('status', 1)
                       ->whereIn('order_id', function($sub) {
                            $sub->select('order_id')
                                ->from('order')
                                ->where('order_status', 3);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

            $totalPoints = 0;
            foreach ($points as $point) {
                if ($point->type === 'credit') {
                    $totalPoints += $point->points;
                } elseif ($point->type === 'debit') {
                    $totalPoints -= $point->points;
                }
            }


            if($points->isNotEmpty())
            {
                $productDataArr = [];
                foreach($points as $index => $data){
                    $productDataArr[] = [
                        'sr_no'=>strval($index + 1),
                        'order_id'=>strval( $data->order_id),
                        'spent_points' => $data->type === 'debit' ? '-' . strval($data->points) : '-',
                        'earned_points' => $data->type === 'credit' ? '+' . strval($data->points) : '-',
                    ];

                }
                $result['code']     =    strval(1);
                $result['message']  =   'success';
                $result['result']   =   $productDataArr; 

            }
            else
            {
                $result['code']     =   strval(1);
                $result['message']  =   'no_data_found';
                $result['result']   =   [];
            }

        }
        else
        {
            $result['code']     =   strval(0);
            $result['message']  =   'no_data_found';
            $result['result']   =   [];
        }
        
        $mainResult = $result;       
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));     
           
    }


    public function addReview(Request $request){
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'product_id' => 'required',
            'order_id' => 'required',
            'no_of_stars' => 'required',
            'review_comment'=>'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }
        $post = $request->all();
        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
        if ($response['code'] != 1) {
            $mainResult   =   $response;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
        
        $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
        
        $addReview = DB::table('ratings')->where('product_id',$request->product_id)->where('order_id',$request->order_id)->where('user_id',$userData->id)->where('status',1)->first();
        
        if($post)
        {   
            // echo "<pre>";print_r($productFav);exit();
            
            $rating = new Rating();
            $rating->user_id = $userData->id;
            $rating->product_id = $request->product_id;  // Replace with the actual product ID
            $rating->order_id = $request->order_id; 
            $rating->ratings = $request->no_of_stars;
            $rating->review = $request->review_comment;
            $rating->status = '1';
            $rating->save();
            
            // $get_average =  \Helper::avrageRating($request->product_id);
            // $update_product =  Product::where('id', $request->product_id)->update(array('average_rating' => $get_average));

            $result['code']     =  strval(1);
            $result['message']  =   'Review Given successfully';
            // $result['result']       = [];
    
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
        else
        {
            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }   
    }

    public function productReviewList(Request $request){
        
        $validator = \Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $result = [];
        $finalArr = [];
        $post = $request->all();
        if($post)
        {
            $page = $request->page;
            if($page==''){
                $page = 1;
            }
            $products = DB::table('product') ->join('ratings','product.id','=','ratings.product_id')->join('main_users','main_users.id','=','ratings.user_id')->select('product.id','ratings.*','main_users.first_name','main_users.last_name')->where('product.id',$request->product_id);
            
            $viewRating = $products->paginate(8, ['*'], 'page', $page);
            if (!empty($viewRating)) {
                $mainArr = [];
                $reviewArr = [];
                $totalPage =[];
                foreach ($viewRating as $data) {
                    $name = $data->first_name.' '.$data->last_name;
                    $review['customer_name'] = strval(@$name);
                    $review['ratings'] = strval(@round($data->ratings));
                    $review['review'] = strval(@$data->review);
                    $review['review_date'] = strval(@\Helper::dateTz($data->created_at));
                    $reviewArr[] = $review;
                }
                $mainArr['product_review'] = $reviewArr;
                $total_products = count($products->get());  
                $result['code']     =  strval(1);
                $result['message']  =   'success';
                $result['total_reviews'] = $total_products;
                $result['result']       = $mainArr;
    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }else{
                $result['code']     =  strval(0);
                $result['message']  =   'no_data_found';
                $result['result']       =   [];
            
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }                
        }else{
            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
      
    }

    public function attachment_quote_email($user_name,$sendname,$sendemail,$category_name,$post_code,$description,$image_name)
    {
        $setting = Setting::find(1);
        $emaildetail = EmailTemplate::find(10);
        $from_email = $setting['mail_username'];
        // echo "<pre>";print_r($setting->toArray());exit();
        // $from_email = 'ritesh.m@vrinsoft.com';
        $logo = asset('assets/dashboard/images/Logo/logo.png');

        $data = array('user_name' => $user_name, 'sendname' => $sendname,'sendemail'=>$sendemail,'category_name'=>$category_name,'post_code'=>$post_code,'description'=>$description,'image_name'=>$image_name,'id'=>10,'from_email' => $from_email);

        Mail::send('emails.quote', $data, function ($message) use ($data, $emaildetail) {

            $message->to($data['sendemail'], 'Trade25')->subject($emaildetail->subject);
            $message->from($data['from_email'], $emaildetail->title);
        });
    }

    public function applyCouponCode($coupon_code){
       // $coupon_code = $request->coupon_code;
        if($coupon_code!=""){
            $promocode_data = Promocode::where('promo_name', $coupon_code)->where('status', 1)->first();
            if ($promocode_data == "")
            {                
                $response = [  'code'  => strval(0),
                                'message' => 'invalid_coupon_code'
                            ];
            }
            elseif ($promocode_data->start_date >= Carbon::now())
            {
                $response = [  'code'  => strval(0),
                                'message' => 'coupon_code_currently_not_active'
                            ];
            }
            elseif (date('Y-m-d') > $promocode_data->end_date)
            {
                $response = [  'code'  => strval(0),
                                'message' => 'coupon_code_expired'
                            ];
            }
            else
            {
                $couponPercentage = $promocode_data->discount_percentage;
                $code_id = $promocode_data->id;
                $response = [  'code'  => strval(1),                                
                                'percentage' =>  strval($couponPercentage),
                                'coupon_id' =>  strval($code_id),
                                'message' => 'coupon_code_applied_successfully',
                            ];
            }
        }else{
            $response = [
                            'code'  => strval(0),
                            'message' => 'not_use'
                        ];
        }  
        
        return $response;
    }

    public function getCartData($language,$product_variants,$coupon_code){
        $variantIds = array();
       
        foreach($product_variants as $variant){
            foreach($variant as $keys => $value){
                $variantIds[] = $keys;
            }               
        } 
        // $new = array_filter($product_variants, function ($var) use ($filterBy) {
        //     return ($var['name'] == $filterBy);
        // });

        $productData = ProductVariants::getProductDetalsBasedOnVariant()->whereIn('id', $variantIds)->get(); 
        if(!empty($productData)){
            $productDataArr = array();
            $org_price = 0;
            $total_discount_price = 0;
            $is_product_discount = [];
            foreach($productData as $data){
                $get_qty = array_column($product_variants, $data->id);
                $get_qty = implode('',$get_qty);
                if ($language == 1) {
                    $title = ($data->get_product_details->product_name_fr) ? $data->get_product_details->product_name_fr : $data->get_product_details->product_name;
                } else {
                    $title = $data->get_product_details->product_name?:'';
                } 
                $product_image = $data->get_product_details->get_product_images->first();
                //$product_variant = $data->get_product_details->get_product_variants->first();
                $product_unit = Helper::getUnitById($data->variant_uof);
                $product_size =  ($data->variant_size) ? $data->variant_size . ' ' . $product_unit : '' ;
                if(file_exists(public_path() . '/uploads/product/' . $product_image->image)){
                    $image_path =  asset('uploads/product/' . $product_image->image);
                }else{
                    $image_path =  asset('assets/frontend/images/image-not-avilable.png');
                }
                        
                $original_price = ($data->variant_price) ? $data->variant_price * $get_qty:0;
                $org_price += $original_price ;
                $variant_org_price = $data->variant_price;

               if($data->variant_discounted_price!='' && $data->variant_discounted_price!=0.00){
                    $is_product_discount[] = true;
                    $discounted_price  = $data->variant_discounted_price  * $get_qty ;
                    $total_discount_price += $original_price - $discounted_price  ;
                    $variant_dis_price = $data->variant_discounted_price;
                }else{
                    $variant_dis_price = 0;
                }  
               $product_variant_stock =  $data->available_qty;

                $productDataArr['cart_list'][] = [
                    'product_id'=>strval(@$data->get_product_details->id),
                    'product_title'=>strval(@$title),
                    'product_size' => strval(@$product_size),
                    'product_image'=> $image_path,
                    'product_orignal_price'=> strval(@$variant_org_price),
                    'product_discounted_price'=>strval(@$variant_dis_price),
                    'cart_id'=>null,
                    'variant_id'=>strval(@$data->id),
                    'product_qty'=>$get_qty,
                    'product_variant_stock'=>$product_variant_stock,                         
                ];            
            }
            $total_products_price = round($org_price, 2);
            if(in_array(true, $is_product_discount)){
                $final_amount = ($total_products_price - $total_discount_price);
            }else{
                $final_amount =  $total_products_price;
            }

            $productDataArr['coupon_discount_amount'] = NULL ;
            if($this->applyCouponCode($coupon_code)){
                $coupon_info = $this->applyCouponCode($coupon_code);
               
                if(isset($coupon_info['percentage'])){
                    $coupon_percentage = $coupon_info['percentage'];
                    $coupon_code_price = ($coupon_percentage / 100) * $final_amount;
                    $coupon_code_price = round($coupon_code_price,2);
                    $final_amount = round(($final_amount - $coupon_code_price), 2);
                    $productDataArr['coupon_discount_amount'] = strval(@$coupon_code_price) ;
                }   
                $productDataArr['coupon_status'] =  $coupon_info;             
            }

            $tax_amount = 0;
            $tax_percentage = 0;
            if(Helper::Settings('tax')!=0){
                $tax_amount = (Helper::Settings('tax') / 100) * $final_amount;
                $tax_percentage = Helper::Settings('tax');
                $total_amount = $final_amount + $tax_amount;
            }else{
                $total_amount = $final_amount;
            }     
            
            
            
            $productDataArr['total_orignal_price'] = strval(@$org_price) ;
            $productDataArr['total_discount_price'] = strval(@$total_discount_price) ;
            $productDataArr['tax_percentage'] = strval(@$tax_percentage) ;
            $productDataArr['tax_amount'] = strval(@$tax_amount) ;
            $productDataArr['grand_total_amount'] = strval(@$total_amount) ;

            
            $result['code']     =    strval(1);
            $result['message']  =   'success';
            $result['result']   =   $productDataArr; 
            $mainResult   =   $result;
            return $mainResult;
        }else{
            $result['code']     =  strval(0);
            $result['message']  =  'no_data_found';
            $result['result']   =  []; 

            $mainResult   =   $result;
            return $mainResult;
        }
    }

    // public function cartList(Request $request){

    //     logger()->info("..................data........................................");
    //     logger()->info(request()->all());

    //     $result = [];
    //     $finalArr = [];
    //     if($request->all()){        
    //         $validator = \Validator::make($request->all(), [
    //             'is_guest' => 'required',
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status_code' => strval(0),
    //                 'error'=>$validator->messages(),
    //                 'data' => null
    //             ], 200);
    //         }
    //         $coupon_code = $request->coupon_code;
    //         if($request->is_guest==1){
    //             $validator = \Validator::make($request->all(), [
    //                 'product_variants' => 'required',
    //             ]);
        
    //             if ($validator->fails()) {
    //                 return response()->json([
    //                     'status_code' => strval(0),
    //                     'error'=>$validator->messages(),
    //                     'data' => null
    //                 ], 200);
    //             }
    //             $product_variants =  $request->product_variants;  
    //             $response = $this->getCartData($request->language,$product_variants,$coupon_code);
    //             return response ()->json(new \App\Http\Resources\V1\SettingResource($response));               
                
    //         }else{
    //             $validator = \Validator::make($request->all(), [
    //                 'uniqid' => 'required',
    //                 'token' => 'required',
    //             ]);
        
    //             if ($validator->fails()) {
    //                 return response()->json([
    //                     'status_code' => strval(0),
    //                     'error'=>$validator->messages(),
    //                     'data' => null
    //                 ], 200);
    //             }
    //             $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
    //             if ($response['code'] != 1) {
    //                 $mainResult   =   $response;
    //                 return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //             }
                
    //             $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
    //             $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $userData->id)->groupBy('user_id')->where('status', 1)->first();
                
    //             if(empty($cartData)){
    //                 $result['code']     =  strval(1);
    //                 $result['message']  =  'no_data_found';
    //                 $result['result']   =  NUll;
                        
    //                 $mainResult   =   $result;
    //                 return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //             }

    //             $variantIds ="";
    //             if(!empty($cartData)){
    //                 $variantIds = explode(',', $cartData->variantIds);
    //                 $productData = ProductVariants::getProductDetalsBasedOnVariant()->whereIn('id', $variantIds)->get(); 
                  
    //                 if(!empty($productData)){
    //                     $productDataArr = array();
    //                     $org_price = 0;
    //                     $total_discount_price = 0;
    //                     $is_product_discount = [];
    //                     foreach($productData as $data){
    //                         $cartInfo = Cart::select('*')->where(['user_id'=>$userData->id,'product_variant_id'=>$data->id])->where('status', 1)->first();
    //                         $get_qty = $cartInfo->quantity;
    //                         $offer_type = $cartInfo->offer_type;
    //                         $discount_amount = $cartInfo->discount_amount;
    //                         $is_offer = (bool) $cartInfo->is_offer;
    //                         $is_bogo = (bool) $cartInfo->is_bogo;
    //                         $offer_label = '';
    //                         $bogo_label = '';

    //                         if($is_bogo)
    //                         {
    //                             $bogo_label = 'BOGO';
    //                         }      

    //                         if($is_offer)
    //                         {
    //                             $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

    //                             if ($offer_type == 'flat') {
    //                                 $offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
    //                             } elseif ($offer_type == 'percentage') {
    //                                 $offer_label = $formatted_discount  . '% off';
    //                             }
    //                         }

                            
    //                         if ($request->language == 1) {
    //                             $title = ($data->get_product_details->product_name_fr) ? $data->get_product_details->product_name_fr : $data->get_product_details->product_name;
    //                         } else {
    //                             $title = $data->get_product_details->product_name?:'';
    //                         } 
    //                         $product_image = $data->get_product_details->get_product_images->first();
    //                         //$product_variant = $data->get_product_details->get_product_variants->first();
    //                         $product_unit = Helper::getUnitById($data->variant_uof);
    //                         $product_size =  ($data->variant_size) ? $data->variant_size . ' ' . $product_unit : '' ;
    //                         if(file_exists(public_path() . '/uploads/product/' . $product_image->image)){
    //                             $image_path =  asset('uploads/product/' . $product_image->image);
    //                         }else{
    //                             $image_path =  asset('assets/frontend/images/image-not-avilable.png');
    //                         }
    //                         $variant_org_price = $data->variant_price;
    //                         $original_price = ($data->variant_price) ? $data->variant_price * $get_qty:0;
    //                         $org_price += $original_price ;
            
    //                        if($data->variant_discounted_price!='' && $data->variant_discounted_price!=0.00){
    //                             $is_product_discount[] = true;   
    //                             $variant_dis_price = $data->variant_discounted_price;                             
    //                             $discounted_price  = $data->variant_discounted_price  * $get_qty ;
    //                             $total_discount_price += $original_price - $discounted_price  ;
    //                        }else{
    //                             $variant_dis_price = 0;
    //                        }  

    //                         $product_variant_stock =  $data->available_qty;
                           
    //                         $productDataArr['cart_list'][] = [
    //                             'product_id'=>strval(@$data->get_product_details->id),
    //                             'product_title'=>strval(@$title),
    //                             'product_size' => strval(@$product_size),
    //                             'product_image'=> $image_path,
    //                             'product_orignal_price'=> strval(@$variant_org_price),
    //                             'product_discounted_price'=>strval(@$variant_dis_price),
    //                             'cart_id'=>strval(@$cartInfo->id),
    //                             'variant_id'=>strval(@$data->id),
    //                             'product_qty'=>$get_qty,   
    //                             'offer_type'=>$offer_type,   
    //                             'bogo_status'=>$is_bogo,   
    //                             'bogo_label' => $bogo_label,
    //                             'discount_amount'=>strval($discount_amount),  
    //                             'offer_status'=>$is_offer,   
    //                             'offer_label'=>$offer_label,   
    //                             'product_variant_stock'=>$product_variant_stock,  

    //                         ];            
    //                     }

    //                     $total_products_price = $org_price;
    //                     if(in_array(true, $is_product_discount)){
    //                         $final_amount = ($total_products_price - $total_discount_price);
    //                     }else{
    //                         $final_amount =  $total_products_price;
    //                     }
            
                        
    //                     $productDataArr['coupon_discount_amount'] = NULL ;
    //                     if($this->applyCouponCode($coupon_code)){
    //                         $coupon_info = $this->applyCouponCode($coupon_code);
                        
    //                         if(isset($coupon_info['percentage'])){
    //                             $coupon_percentage = $coupon_info['percentage'];
    //                             $coupon_code_price = ($coupon_percentage / 100) * $final_amount;
    //                             $coupon_code_price = round($coupon_code_price,2);
    //                             $total_amount = round(($final_amount - $coupon_code_price), 2);
    //                             $productDataArr['coupon_discount_amount'] = strval(@$coupon_code_price) ;
    //                         }   
    //                         $productDataArr['coupon_status'] =  $coupon_info;             
    //                     }

    //                     $tax_amount = 0;
    //                     $tax_percentage = 0;
    //                     if(Helper::Settings('tax')!=0){
    //                         $tax_amount = (Helper::Settings('tax') / 100) * $final_amount;
    //                         $tax_percentage = Helper::Settings('tax');
    //                         $total_amount = $final_amount + $tax_amount;
    //                     }else{
    //                         $total_amount = $final_amount;
    //                     }            
                        

    //                     $productDataArr['total_orignal_price'] = strval(@$org_price) ;
    //                     $productDataArr['total_discount_price'] = strval(@$total_discount_price) ;
    //                     $productDataArr['tax_percentage'] = strval(@$tax_percentage) ;
    //                     $productDataArr['tax_amount'] = strval(@$tax_amount) ;
    //                     $productDataArr['grand_total_amount'] = strval(@$total_amount) ;

                        
    //                     $result['code']     =    strval(1);
    //                     $result['message']  =   'success';
    //                     $result['result']   =   $productDataArr; 
    //                     $mainResult   =   $result;
    //                     return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //                 }else{
    //                     $result['code']     =  strval(1);
    //                     $result['message']  =  'no_data_found';
    //                     $result['result']   =  []; 

    //                     $mainResult   =   $result;
    //                     return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //                 }
    //             } 
    //             $response = $this->getCartData($request->language,$variantIds);
    //             return response ()->json(new \App\Http\Resources\V1\SettingResource($response));
    //         }
    //     }else{
       
    //         $result['code']     =  strval(0);
    //         $result['message']  =  'something_went_wrong';
    //         $result['result']   =  [];
                
    //         $mainResult   =   $result;
    //         return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     }
    // }

    // 2 sep 2025
     public function cartList(Request $request){

        $result = [];
        $finalArr = [];
        if($request->all()){        
            $validator = \Validator::make($request->all(), [
                'is_guest' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status_code' => strval(0),
                    'error'=>$validator->messages(),
                    'data' => null
                ], 200);
            }
            $coupon_code = $request->coupon_code;
            if($request->is_guest==1){
                $validator = \Validator::make($request->all(), [
                    'product_variants' => 'required',
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error'=>$validator->messages(),
                        'data' => null
                    ], 200);
                }
                $product_variants =  $request->product_variants;  
                $response = $this->getCartData($request->language,$product_variants,$coupon_code);
                return response ()->json(new \App\Http\Resources\V1\SettingResource($response));               
                
            }else{
                $validator = \Validator::make($request->all(), [
                    'uniqid' => 'required',
                    'token' => 'required',
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error'=>$validator->messages(),
                        'data' => null
                    ], 200);
                }
                $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
                if ($response['code'] != 1) {
                    $mainResult   =   $response;
                    return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
                
                $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
                $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $userData->id)->groupBy('user_id')->where('status', 1)->first();
                
                if(empty($cartData)){
                    $result['code']     =  strval(1);
                    $result['message']  =  'no_data_found';
                    $result['result']   =  NUll;
                        
                    $mainResult   =   $result;
                    return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }

                $variantIds ="";
                if(!empty($cartData)){
                    $variantIds = explode(',', $cartData->variantIds);
                    $productData = ProductVariants::getProductDetalsBasedOnVariant()->whereIn('id', $variantIds)->get(); 
                  
                    if(!empty($productData)){
                        $productDataArr = array();
                        $org_price = 0;
                        $total_discount_price = 0;
                        // $is_product_discount = [];
                        $is_product_discount = false;
                         $total_quantity=0;
                        foreach($productData as $data){
                            $cartInfo = Cart::select('*')->where(['user_id'=>$userData->id,'product_variant_id'=>$data->id])->where('status', 1)->first();
                            $get_qty = $cartInfo->quantity;
                            $offer_type = $cartInfo->offer_type ?? null;
                            $discount_amount = $cartInfo->discount_amount ?? null;
                            $display_discount_amount=$discount_amount;
                            $is_offer = (bool) $cartInfo->is_offer;
                            $is_bogo = (bool) $cartInfo->is_bogo;
                            $offer_label = '';
                            $bogo_label = '';

                            if($is_bogo)
                            {
                                $bogo_label = 'BOGO';
                            }      

                            if($is_offer)
                            {
                                $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

                                if ($offer_type == 'flat') {
                                    $offer_label = 'Flat ' . $formatted_discount  . ' GH₵ off';
                                } elseif ($offer_type == 'percentage') {
                                    $offer_label = $formatted_discount  . '% off';
                                }
                            }

                            
                            if ($request->language == 1) {
                                $title = ($data->get_product_details->product_name_fr) ? $data->get_product_details->product_name_fr : $data->get_product_details->product_name;
                            } else {
                                $title = $data->get_product_details->product_name?:'';
                            } 
                            $product_image = $data->get_product_details->get_product_images->first();
                            //$product_variant = $data->get_product_details->get_product_variants->first();
                            $product_unit = Helper::getUnitById($data->variant_uof);
                            $product_size =  ($data->variant_size) ? $data->variant_size . ' ' . $product_unit : '' ;
                            if(file_exists(public_path() . '/uploads/product/' . $product_image->image)){
                                $image_path =  asset('uploads/product/' . $product_image->image);
                            }else{
                                $image_path =  asset('assets/frontend/images/image-not-avilable.png');
                            }

                            $display_price = ($data->variant_price) ? $data->variant_price : 0;
                            $display_discount_price = $display_price;

                            // Apply offer discount for 1 unit
                            if (!$is_bogo && $is_offer) {
                                $formatted_discount = rtrim(rtrim(number_format($discount_amount, 2, '.', ''), '0'), '.');

                                if ($offer_type == 'flat') {
                                    $display_discount_price = max(0, $display_price - $discount_amount);
                                } elseif ($offer_type == 'percentage') {
                                    $display_discount_price = max(0, $display_price - ($display_price * $discount_amount / 100));
                                    $display_discount_amount = max(0, $display_price * $formatted_discount / 100);
                                }
                            } elseif ($data->variant_discounted_price && $data->variant_discounted_price != 0) {
                                $display_discount_price = $data->variant_discounted_price;
                            }

                            $original_price = ($data->variant_price) ? $data->variant_price * $get_qty:0;
                            // $org_price += $original_price ;
                            $discount_price = $original_price;

                            // ✅ Apply offer or variant discount
                            if (!$is_bogo && $is_offer) {
                                if ($offer_type == 'flat') {
                                    $discount_price = max(0, $original_price - ($discount_amount * $get_qty));
                                } elseif ($offer_type == 'percentage') {
                                    $discount_price = max(0, $original_price - ($original_price * $discount_amount / 100));
                                }
                                $is_product_discount = true;
                            } elseif ($data->variant_discounted_price && $data->variant_discounted_price != 0) {
                                $discount_price = $data->variant_discounted_price * $get_qty;
                                $is_product_discount = true;
                            }

                            // ✅ Accumulate totals
                            $org_price += $original_price;
                            $total_discount_price += ($original_price - $discount_price);

                            $product_variant_stock =  $data->available_qty;

                            $item_quantity = $get_qty;
                            if ($is_bogo) {
                                $item_quantity = $get_qty * 2;
                            }
                            $total_quantity += $item_quantity;

                           
                            $productDataArr['cart_list'][] = [
                                'product_id'=>strval(@$data->get_product_details->id),
                                'product_title'=>strval(@$title),
                                'product_size' => strval(@$product_size),
                                'product_image'=> $image_path,
                                // 'product_orignal_price'=> strval(@$original_price),
                                // 'product_discounted_price'=>strval(@$discount_price),
                                'product_orignal_price'=> strval(@$display_price),
                                'product_discounted_price'=>strval(@$display_discount_price),
                                'cart_id'=>strval(@$cartInfo->id),
                                'variant_id'=>strval(@$data->id),
                                'product_qty'=>$get_qty,   
                                'offer_type'=>$offer_type,   
                                'bogo_status'=>$is_bogo,   
                                'bogo_label' => $bogo_label,
                                'discount_amount'=>strval($display_discount_amount),  
                                'offer_status'=>$is_offer,   
                                'offer_label'=>$offer_label,   
                                'product_variant_stock'=>$product_variant_stock,  

                            ];            
                        }

                         // Final amount calculation
                        $final_amount = $org_price - $total_discount_price;
            
                        
                        $productDataArr['coupon_discount_amount'] = NULL ;
                        if($this->applyCouponCode($coupon_code)){
                            $coupon_info = $this->applyCouponCode($coupon_code);
                        
                            if(isset($coupon_info['percentage'])){
                                $coupon_percentage = $coupon_info['percentage'];
                                $coupon_code_price = ($coupon_percentage / 100) * $final_amount;
                                $coupon_code_price = round($coupon_code_price,2);
                                $total_amount = round(($final_amount - $coupon_code_price), 2);
                                $productDataArr['coupon_discount_amount'] = strval(@$coupon_code_price) ;
                            }   
                            $productDataArr['coupon_status'] =  $coupon_info;             
                        }

                        $tax_amount = 0;
                        $tax_percentage = 0;
                        if(Helper::Settings('tax')!=0){
                            $tax_amount = (Helper::Settings('tax') / 100) * $final_amount;
                            $tax_percentage = Helper::Settings('tax');
                            $total_amount = $final_amount + $tax_amount;
                        }else{
                            $total_amount = $final_amount;
                        }            
                        

                        $productDataArr['total_orignal_price'] = strval(@$org_price) ;
                        $productDataArr['total_quantity'] =$total_quantity;
                        $productDataArr['total_discount_price'] = strval(@$total_discount_price) ;
                        $productDataArr['tax_percentage'] = strval(@$tax_percentage) ;
                        $productDataArr['tax_amount'] = strval(@$tax_amount) ;
                        $productDataArr['grand_total_amount'] = strval(@$total_amount) ;

                        
                        $result['code']     =    strval(1);
                        $result['message']  =   'success';
                        $result['result']   =   $productDataArr; 
                        $mainResult   =   $result;
                        return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                    }else{
                        $result['code']     =  strval(1);
                        $result['message']  =  'no_data_found';
                        $result['result']   =  []; 

                        $mainResult   =   $result;
                        return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                    }
                } 
                $response = $this->getCartData($request->language,$variantIds);
                return response ()->json(new \App\Http\Resources\V1\SettingResource($response));
            }
        }else{
       
            $result['code']     =  strval(0);
            $result['message']  =  'something_went_wrong';
            $result['result']   =  [];
                
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }


    public function viewCart(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

         if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();

            $cartData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->leftJoin('main_users','main_users.id','=','product.supplier_id')->select('cart.*','product.product_image','product.uniqid as product_unique_id','product.product_name','main_users.store_name','main_users.id as store_id')->where('cart.user_id',$userData->id)->where('cart.status',1)->where('product.status',1)->where('product.is_admin_approve',1)->get();

            if (!empty($cartData->toArray())) {
                $cartItemCount = $cartData->count();

            $cartTotalPrice = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.user_id',$userData->id)->where('cart.status',1)->where('product.status',1)->where('product.is_admin_approve',1)->sum('cart.product_price');
            $cartDiscountPrice = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.user_id',$userData->id)->where('cart.status',1)->where('product.status',1)->where('product.is_admin_approve',1)->sum('cart.offer_price');
            // echo "<pre>";print_r($cartData->toArray());exit();
            if($cartDiscountPrice != 0)
            {
                $totalDicountPrice = @$cartTotalPrice - @$cartDiscountPrice;
                $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;
            }
            else
            {
                $totalDicountPrice = @$cartDiscountPrice;
                $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;
            }
            // $totalDicountPrice = @$cartTotalPrice - @$cartDiscountPrice;

            // $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;

            $mainArr = [];

            $mainArr['cartTotalPrice'] = strval(@$cartTotalPrice);
            $mainArr['totalCartAmount'] = strval(@$totalCartAmount);
            $mainArr['totalDicountPrice'] = strval(@$totalDicountPrice);

            // echo "<pre>";print_r($cartData->toArray());exit();
            $cartArr = [];
            foreach ($cartData as $cart) {
                $cartList['cart_id'] = strval(@$cart->id);
                $cartList['product_id'] = strval(@$cart->product_id);
                $cartList['store_id'] = strval(@$cart->store_id);
                $cartList['store_name'] = strval(@$cart->store_name);
                $cartList['product_name'] = strval(@$cart->product_name);
                $cartList['product_img'] = strval(@$cart->product_image ? asset( PRODUCT_PATH . $cart->product_image) : '');
                $cartList['quantity'] = strval(@$cart->quantity);
                $cartList['product_discounted_price'] = strval(@$cart->total_price);
                $cartList['product_price'] = strval(@$cart->product_price);
                $cartArr[] = $cartList;
            }
                $mainArr['product_list'] = $cartArr;

                $result['code']     =  strval(1);
                $result['message']  =   'success';
                $result['result'][]   = $mainArr;
        
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }else{
                 $result['code']     =  strval(0);
                    $result['message']  =   'no_data_found';
                    $result['result']       =  [];
        
                    $mainResult   =   $result;
                    return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
            

        }else{
             $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
                
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function removeProductFromCart(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'cart_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

         if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();

            if (!empty($request->cart_id)) {

             $editFavorite = Cart::where('user_id', $userData->id)->where('id',$request->cart_id)->update(array(
                            'status' => 2,
                        ));

             $result['code']     =  strval(1);
             $result['message']  =   'success';
             // $result['result']   = [];
        
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

            }else{

                    $result['code']     =  strval(0);
                    $result['message']  =   'no_data_found';
                    $result['result']       =  [];
        
                    $mainResult   =   $result;
                    return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }else{

            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
                
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function removeAllFav(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            // 'search_text' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        if ($request->uniqid) {
            $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }
        }
         

        if($post)
        {

            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();

            $editFavorite = FavoriteProduct::where('user_id', $userData->id)->update(array(
                            'status' => 2,
                        ));
            
                if ($editFavorite) {
                    $result['code']     =  strval(1);
                $result['message']  =   'success';
                // $result['result'][] = [];
        
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }else{
                    $result['code']     =  strval(0);
                $result['message']  =   'something_went_wrong';
                $result['result']       =   [];
                
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
                
        }else{

                $result['code']     =  strval(0);
                $result['message']  =   'something_went_wrong';
                $result['result']       =   [];
                
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function notificationCount(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        if ($request->uniqid) {
            $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
            if ($response['code'] != 1) {
                $mainResult   =   $response;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }
        if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $notificationData = DB::table('notification as N')
                                ->select('N.notification_type','N.created_at','N.id as nid','N.order_id as order_id','N.title','N.message','N.is_read','O.order_id as order_number')
                                ->join('order as O','N.order_id','O.id')->where('sender_id',$userData->id)->where('is_read',0)->orderby('N.id','DESC');
            $notificationDataCount = $notificationData->count();
    

            $mainArr = [];
            if (!empty($notificationData)) {
                
                $mainArr['notification_count'] = strval(@$notificationDataCount);

                $result['code']     =  strval(1);
                $result['message']  =  'success';
                $result['result']   = $mainArr;
    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }else{

                $result['code']     =  strval(0);
                $result['message']  =   'no_data_found';
                $result['result']       =  [];
    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }else{

            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }
    public function notificationList(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'page' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        if ($request->uniqid) {
            $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
            if ($response['code'] != 1) {
                $mainResult   =   $response;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }
        if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $editNotification = Notification::where('sender_id', $userData->id)->update(array(
                                    'is_read' => 1,
                                ));

            $notificationData = DB::table('notification as N')
                                ->select('N.notification_type','N.created_at','N.id as nid','N.order_id as order_id','N.title','N.message','N.is_read','O.order_id as order_number')
                                ->join('order as O','N.order_id','O.id')->where('sender_id',$userData->id)->orderby('N.id','DESC');
            $notificationDataCount = $notificationData->get();
            $notificationData = $notificationData->paginate(10, ['*'], 'page', $request->page);
    

            $mainArr = [];
            $notificationArr = [];

            $mainArr['total_records'] = strval(@$notificationDataCount->count());
            $mainArr['records_per_page'] = "10";
            $mainArr['current_page'] = @$request->page;

            if (!empty($notificationData)) {
                foreach ($notificationData as $notification) {

                if ($notification->notification_type == 1) {
                    $icon = "fa-solid fa-check text-dark-green";
                }elseif ($notification->notification_type == 2) {
                    $icon = "fa-solid fa-image text-orange";
                    
                }elseif ($notification->notification_type == 3) {
                    $icon = "fa-solid fa-van-shuttle text-pink";
                    
                }elseif ($notification->notification_type == 4) {
                    $icon = "fa-solid fa-check text-dark-green";
                    
                }elseif ($notification->notification_type == 5) {
                    $icon = "fa-solid fa-xmark text-dark-green";
                    
                }else{
                    $icon = "fa-solid fa-image text-orange";

                }

                //$date_time = \Helper::converttimeTozone($notification->created_at);
                //$date_time = date('h:i A d M, Y',strtotime($date_time));

                $notificationList['notification_id'] = strval(@$notification->nid);
                $notificationList['order_id'] = strval(@$notification->order_id);
                $notificationList['order_number'] = strval(@$notification->order_number);
                $notificationList['notification_type'] = strval(@$notification->notification_type);
                $notificationList['title'] = strval(@$notification->title);
                $notificationList['message'] = strval(@$notification->message);
                $notificationList['icon_name'] = strval(@$icon);
                $notificationList['is_read'] = strval(@$notification->is_read);
                $notificationList['date_and_time'] = strval(@\Helper::dateTz($notification->created_at));
                $notificationArr[] = $notificationList;

            }
            $mainArr['notification_list'] = $notificationArr;

                $result['code']     =  strval(1);
                $result['message']  =   'success';
                $result['result']   = $mainArr;
    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }else{

                $result['code']     =  strval(0);
                $result['message']  =   'no_data_found';
                $result['result']       =  [];
    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }else{

            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function notificationListOld31(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'page' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        if ($request->uniqid) {
            $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
            if ($response['code'] != 1) {
                $mainResult   =   $response;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }
        if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $editNotification = Notification::where('sender_id', $userData->id)->update(array(
                                    'is_read' => 1,
                                ));

            $notificationData = DB::table('notification')->where('sender_id',$userData->id);
            $notificationDataCount = $notificationData->get();
            $notificationData = $notificationData->paginate(10, ['*'], 'page', $request->page);
            // echo "<pre>";print_r($notificationData->toArray());exit();

            $mainArr = [];
            $notificationArr = [];

            $mainArr['total_records'] = strval(@$notificationDataCount->count());
            $mainArr['records_per_page'] = "10";
            $mainArr['current_page'] = @$request->page;

            if (!empty($notificationData)) {
                foreach ($notificationData as $notification) {

                if ($notification->notification_type == 1) {
                    $icon = "fa-solid fa-check text-dark-green";
                }elseif ($notification->notification_type == 2) {
                    $icon = "fa-solid fa-image text-orange";
                    
                }elseif ($notification->notification_type == 3) {
                    $icon = "fa-solid fa-van-shuttle text-pink";
                    
                }elseif ($notification->notification_type == 4) {
                    $icon = "fa-solid fa-check text-dark-green";
                    
                }elseif ($notification->notification_type == 5) {
                    $icon = "fa-solid fa-xmark text-dark-green";
                    
                }else{
                    $icon = "fa-solid fa-image text-orange";

                }

                $date_time = \Helper::converttimeTozone($notification->created_at);
                $date_time = date('h:i A d M, Y',strtotime($date_time));

                $notificationList['notification_id'] = strval(@$notification->id);
                $notificationList['order_id'] = strval(@$notification->order_id);
                $notificationList['notification_type'] = strval(@$notification->notification_type);
                $notificationList['title'] = strval(@$notification->title);
                $notificationList['message'] = strval(@$notification->message);
                $notificationList['icon_name'] = strval(@$icon);
                $notificationList['is_read'] = strval(@$notification->is_read);
                $notificationList['date_and_time'] = strval(@$date_time);
                $notificationArr[] = $notificationList;
            }
            $mainArr['notification_list'] = $notificationArr;

                $result['code']     =  strval(1);
                $result['message']  =   'success';
                $result['result']   = $mainArr;
    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }else{

                $result['code']     =  strval(0);
                $result['message']  =   'no_data_found';
                $result['result']       =  [];
    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        }else{

            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
            
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function shopList(Request $request)
    {
        $result = [];
        $finalArr = [];
        $sort = '';

        if ($request->uniqid) {
            $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }
        }

            $category_id = @$request->category_id;
            $min_price = @$request->min_price;
            $max_price = @$request->max_price;

         $product_data = DB::table('product')->leftjoin('main_users','main_users.id','=','product.supplier_id')->leftJoin('categories','categories.id','=','product.category_id')->select('product.*','main_users.first_name','main_users.last_name','categories.title as category_name')->where('categories.status',1)->where('product.status',1)->where('product.is_admin_approve',1);

         if ($category_id) {
                $product_data = $product_data->where('product.category_id',$category_id);
            }

            // $product_discount_data = DB::table('product')->leftjoin('main_users','main_users.id','=','product.supplier_id')->leftJoin('categories','categories.id','=','product.category_id')->select('product.*','main_users.first_name','main_users.last_name','categories.title as category_name')->where('categories.status',1)->where('product.status',1)->where('product.is_admin_approve',1)->get();

            // if(isset($product_discount_data))
            // {
            //     foreach($product_discount_data as $pdd)
            //     {
            //         if($pdd->discount_price != '0')
            //         {
            //             $sort = "product.discount_price";
            //         }
            //         else
            //         {
            //             $sort = "product.retail_price";
            //         }
            //     }
            // }
            // else
            // {
            //     $sort = "product.retail_price";
            // }

            $sort = "product.retail_price";

            if ($request->price_filter == 0) {
                $sortBy = "ASC";
            }
            if ($request->price_filter == 1) {
                $sortBy = "DESC";
            }
            $product_data = $product_data->orderBy($sort,$sortBy);
            

            
            $product_data_count = $product_data->get();
            $product_data = $product_data->paginate(10, ['*'], 'page', $request->page);

            if (!$product_data->isEmpty()) {
                    $mainArr = [];

                      $mainArr['total_records'] = strval(@$product_data_count->count());
                        $mainArr['records_per_page'] = "10";
                        $mainArr['current_page'] = @$request->page;
                    
                    // $mainArr['desc'] = strval(@$store_data->profile);
                   
                   
                    $productArr=[];
                    if ($request->uniqid) {
                        $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->where('status',1)->first();
                    }

                    foreach ($product_data as $data) {
                        if ($request->uniqid) {
                            
                        $fav_data = DB::table('favorite_product')->where('product_id',$data->id)->where('user_id',$userData->id)->where('status',1)->first();
                        }
                        $product['product_id'] = strval(@$data->id);
                        $product['store_id'] = strval(@$data->supplier_id);
                        $product['product_name'] = strval(@$data->product_name);
                        $product['product_img'] = strval(@$data->product_image ? asset( PRODUCT_PATH . $data->product_image) : '');
                        $product['short_description'] = strval(@$data->short_description);
                        $product['product_original_price'] = strval(@$data->retail_price);
                        $product['product_discounted_price'] = strval(@$data->discount_price);
                        $product['category_id'] = strval(@$data->category_id);
                        $product['is_fav'] = strval(@$fav_data->status ? $fav_data->status : "0");
                        $productArr[] = $product;
                    }

                    $mainArr['product_list'] = $productArr;
                    $result['code']     =  strval(1);
                    $result['message']  =   'success';
                    $result['result']['data']       = $mainArr;
        
                    $mainResult   =   $result;
                    return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }else{
                    $result['code']     =  strval(0);
                    $result['message']  =   'no_data_found';
                    $result['result']       =  [];
        
                    $mainResult   =   $result;
                    return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
         // echo "<pre>";print_r($product_data->toArray());exit();
    }

    public function storeMap(Request $request)
    {
        // echo "string";exit();
        $result = [];
        $finalArr = [];
        // $validator = \Validator::make($request->all(), [
        //     // 'uniqid' => 'required',
        //     // 'token' => 'required',
        //     // 'latitude' => 'required',
        //     // 'longitude' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status_code' => strval(0),
        //         'error'=>$validator->messages(),
        //         'data' => null
        //     ], 200);
        // }

        // $post = $request->all();

        if ($request->uniqid) {
            $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

        }

        //  if($post)
        // {
            $current_lat = $request->latitude;
            $current_long = $request->longitude;
            $map_distance = Setting::find(1);
            $diff = $map_distance->map_distance;
    // print_r($current_long);exit();
        // $storeMapData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile',DB::raw("(3959 * acos(cos(radians('" . $current_lat . "')) * cos(radians(store_details.latitude)) * cos( radians(store_details.longitude) - radians('" . $current_long . "')) + sin(radians('" . $current_lat . "')) * sin(radians(store_details.latitude))))* 0.621371 as distance"))->havingRaw('distance <='.$diff)->orderby('distance','DESC')->get();
        // // echo "<pre>";print_r($storeMapData);exit();
        // if (empty($storeMapData->toArray())) {
        //     // echo "string";exit();
        //     $storeMapData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile')->orderby('store_details.id','DESC')->get();
        // }

        if ($current_lat) {
        // echo "string";exit;
         $storeMapData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile',DB::raw("(3959 * acos(cos(radians('" . $current_lat . "')) * cos(radians(store_details.latitude)) * cos( radians(store_details.longitude) - radians('" . $current_long . "')) + sin(radians('" . $current_lat . "')) * sin(radians(store_details.latitude))))* 0.621371 as distance"))->where('store_details.status',1)->havingRaw('distance <='.$diff)->orderby('distance','DESC')->get();

    }else{
        // echo "string2";exit;
        $storeMapData = DB::table('store_details')->leftjoin('main_users','main_users.id','=','store_details.wholesaler_id')->select('store_details.*','main_users.store_name','main_users.profile')->orderby('store_details.id','DESC')->where('store_details.status',1)->get();
    }

        // echo "<pre>";print_r($storeMapData->toArray());exit();
        if (!empty($storeMapData->toArray())) {
                $mainArr = [];
                $mapArr = [];
                foreach ($storeMapData as $map) {

                    $storeTiming = DB::table('store_timing_week')->leftjoin('week_list','week_list.id','=','store_timing_week.week_id')->where('store_timing_week.status',1)->where('store_timing_week.store_id',$map->id)->select('store_timing_week.*','week_list.name as week_name')->get();
                    // echo "<pre>";print_r($storeTiming->toArray());exit();
                    $mapList['id'] = strval($map->id);
                    $mapList['store_id'] = strval($map->wholesaler_id);
                    $mapList['store_name'] = strval($map->store_name);
                    $mapList['store_logo'] = strval(@$map->profile ? asset( CUSTOMER_PROFILE_PATH . $map->profile) : '');
                    $mapList['latitude'] = strval($map->latitude);
                    $mapList['longitude'] = strval($map->longitude);

                $timeArr = [];
                    foreach ($storeTiming as $time) {
                        $timeList['id'] = $time->id;
                        $timeList['start_time'] = $time->start_time;
                        $timeList['end_time'] = $time->end_time;
                        $timeList['week_name'] = $time->week_name;
                        
                        $timeArr[] = $timeList;
                    }
                    $mapList['timing'] = $timeArr;
                    $mapArr[] = $mapList;
                }

                $mainArr['product_list'] = $mapArr;

                $result['code']     =  strval(1);
                $result['message']  =   'success';
                $result['result'][]   = $mainArr;
            
                 $mainResult   =   $result;
                 return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            
        }else{

                    $result['code']     =  strval(0);
                    $result['message']  =   'no_data_found';
                    $result['result']       =  [];
        
                    $mainResult   =   $result;
                    return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        // }else{
            
        //         $result['code']     =  strval(0);
        //         $result['message']  =   'something_went_wrong';
        //         $result['result']       =   [];
                
        //         $mainResult   =   $result;
        //         return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

        // }   
    }
}
