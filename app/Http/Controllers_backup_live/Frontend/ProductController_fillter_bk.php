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

class ProductController extends Controller
{    
    public function __construct()
    {        
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';       
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
    
    public function filterData(Request $request)
    {
        $product_last_id_for_pagination = $request->product_last_id;
        $limit = 16;
        $brand_ids = array();
        $category_ids = null;
        $subcategory_ids = null;
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
       //dd(Session::get('cart_info'));
        $id = base64_decode($id);

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
            Alert::error('Error', __(Helper::language('something_went_wrong')));
            return redirect('/');
        }
        $product_subcategoryid = $product_info->subcategory_id;
        $relatedProduct = Product::activeProductsBasedOnRelations()->where('product.subcategory_id',$product_subcategoryid)->where('product.id',"!=",$id)->limit(6)->get();
       
        $subCatgyIds = MostViewProduct::getMostViewProductSubcategoryIds();
      
        $recommendedProduct = Product::activeProductsBasedOnRelations()->whereIn('product.subcategory_id',$subCatgyIds)->where('product.id',"!=",$id)->limit(6)->get();
     

        $productRating = Product::productUserRating($id);
        // dd($id);
        return view("frontend.product.product-detail",compact('product_info','settings','relatedProduct','productRating','recommendedProduct','id'));
    }
    /*--This function is using for fetching variant price. 
    when user click on pack size on product details page.---*/
    public function productVariantPrice(Request $request){
        $varinat_id = base64_decode($request->variantId);
        $varinat_info = ProductVariants::where('id',$varinat_id)->first();
        $instock = false;
        if($varinat_info->variant_qty > 0){
            $instock = true;
        }
        if($varinat_info->variant_discounted_price=='' || $varinat_info->variant_discounted_price==0)
        {
            $varinat_disocunt_price = $varinat_info->variant_price ;
        }else{
            $varinat_disocunt_price = $varinat_info->variant_discounted_price ;
        }
        return response()->json(['orignal_price' => $varinat_info->variant_price, 'discounted_price' => $varinat_disocunt_price,'product_stock'=>$instock]);
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
    // public function productCartAdd(Request $request){
    //     //  Session::flush();
    //     $product_id = base64_decode($request->product_id);
    //     $quantity = $request->quantity;
    //     $variant_id = $request->variantId;
    //     Helper::cartItem($product_id,$variant_id,$quantity);
    //     if(!isset(auth()->guard('user')->user()->id)){

    //     }   

    // }
    // public function productCartAddBKKKKUP(Request $request){
    //     //  Session::flush();
    //     $product_id = base64_decode($request->product_id);
    //     $quantity = $request->quantity;
    //     $variant_id = $request->variantId;        

    //     if(Auth::guard('user')->user()==""){
    //         $get_cart_array = $request->session()->get('cart_info');
    //         if($get_cart_array==""){
    //             /*---Creating new product array for cart.--*/
    //             $cart_info = array($product_id=>array($variant_id=>$quantity));
    //             Session::put('cart_info',$cart_info);
    //         }else{
    //             /*---session have cart values if product is exits.--*/
    //             if(array_key_exists($product_id,$get_cart_array)){
    //                 foreach($get_cart_array as $key => $variant_array){     
    //                     if($key==$product_id && array_key_exists($variant_id,$variant_array)){                       
    //                         $new_array = array($product_id=>array($variant_id=>$quantity));
    //                         $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
    //                         $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
    //                         $result = array_replace_recursive($get_cart_array, $new_array);                      
    //                         Session::put('cart_info',$result);
    //                     }else{
    //                         $new_array = array($product_id=>array($variant_id=>$quantity));
    //                         $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
    //                         $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
    //                         $result = array_replace_recursive($get_cart_array, $new_array);                      
    //                         Session::put('cart_info',$result);
    //                     }
    //                 }
    //             }else{
    //                 /*---combine if already product in cart and add new cart product in sesstion.--*/
    //                 $new_array = array($product_id=>array($variant_id=>$quantity));
    //                 $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
    //                 $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
    //                 $result = array_replace_recursive($get_cart_array, $new_array);
    //                 Session::put('cart_info',$result);          
    //             }
    //         }
    //         $get_cart_count = count(Session::get('cart_info'));
    //         return response()->json(['success' => 'true','cart_count'=>$get_cart_count]);
    //     }else{
    //         $user_id = Auth::guard('user')->user()->id;
    //         /*-- Get product variant details---*/
    //         $product_variant_details = DB::table('product')->leftjoin('product_variants', 'product_variants.product_id', '=', 'product.id')->where('product.id', $product_id)->where('product_variants.id', $variant_id)->select('product_variants.*')->first();
    //             if($product_variant_details->variant_discounted_price=='' || $product_variant_details->variant_discounted_price==0.0){                    
    //                 $total_price = $quantity * (isset($product_variant_details->variant_price) ? $product_variant_details->variant_price : '0');
    //             }else{
    //                 $total_price = $quantity * (isset($product_variant_details->variant_discounted_price) ? $product_variant_details->variant_discounted_price : '0');
    //             }

    //         /*--- Check user request data is in cart or not ---*/
    //         $cartData = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $user_id)->where('cart.product_id', $product_id)->where('product_variant_id', $variant_id)->where('cart.status', 1)->select('cart.*')->first();

    //         if (!empty($cartData)) {
    //             if($product_variant_details->variant_discounted_price=="0.00"){
    //                 $product_price = $product_variant_details->variant_price;
    //             }else{
    //                 $product_price = $product_variant_details->variant_discounted_price;
    //             }
    //             $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $product_id)->where('product_variant_id', $variant_id)->update(array(
    //                 'product_price' => $product_price,        
    //                 'quantity' => $quantity,
    //                 'total_price' => $total_price,
    //             ));
    //         } else {
    //             if($product_variant_details->variant_discounted_price=="0.00"){
    //                 $product_price = $product_variant_details->variant_price;
    //             }else{
    //                 $product_price = $product_variant_details->variant_discounted_price;
    //             }
               
    //             $uniqid = uniqid();
    //             $cart = new Cart();
    //             $cart->uniqid = $uniqid;
    //             $cart->product_id = $product_id;
    //             $cart->product_variant_id = $product_variant_details->id;
    //             $cart->product_price = $product_price;
    //           //  $cart->product_price = $product_variant_details->variant_discounted_price;
    //             $cart->user_id = $user_id;
    //             $cart->quantity = $quantity;
    //             $cart->total_price =  $total_price;
    //             $cart->status = 1;
    //             $cart->save();
    //         }
    //         $totalCount = \DB::table('cart')->where('user_id',$user_id)->where('status','1')->count();
    //         return response()->json(['success' => 'true','cart_count'=>@$totalCount?:0]);
    //     }       
    // }

    public function productCartAdd(Request $request){
        //  Session::flush();
        $product_id = base64_decode($request->product_id);
        $quantity = $request->quantity;
        $variant_id = $request->variantId;        

        if(Auth::guard('user')->user()==""){
            $get_cart_array = $request->session()->get('cart_info');
            if($get_cart_array==""){
                /*---Creating new product array for cart.--*/
                $cart_info = array($product_id=>array($variant_id=>$quantity));
                Session::put('cart_info',$cart_info);
            }else{
                /*---session have cart values if product is exits.--*/
                if(array_key_exists($product_id,$get_cart_array)){
                    foreach($get_cart_array as $key => $variant_array){     
                        if($key==$product_id && array_key_exists($variant_id,$variant_array)){                       
                            $new_array = array($product_id=>array($variant_id=>$quantity));
                            $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
                            $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
                            $result = array_replace_recursive($get_cart_array, $new_array);                      
                            Session::put('cart_info',$result);
                        }else{
                            $new_array = array($product_id=>array($variant_id=>$quantity));
                            $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
                            $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
                            $result = array_replace_recursive($get_cart_array, $new_array);                      
                            Session::put('cart_info',$result);
                        }
                    }
                }else{
                    /*---combine if already product in cart and add new cart product in sesstion.--*/
                    $new_array = array($product_id=>array($variant_id=>$quantity));
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

            if (!empty($cartData)) {
                ///if($product_variant_details->variant_discounted_price=="0.00"){
                    $product_price = $product_variant_details->variant_price;
                //}else{
                  //  $product_price = $product_variant_details->variant_discounted_price;
                  $offer_price = $product_variant_details->variant_discounted_price;
                //}
                $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $product_id)->where('product_variant_id', $variant_id)->update(array(
                    'product_price' => $product_price,   
                    'offer_price' =>  $offer_price,    
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                ));
            } else {
                //if($product_variant_details->variant_discounted_price=="0.00"){
                    $product_price = $product_variant_details->variant_price;
                //}else{
                    $offer_price = $product_variant_details->variant_discounted_price;
                //}
               
                $uniqid = uniqid();
                $cart = new Cart();
                $cart->uniqid = $uniqid;
                $cart->product_id = $product_id;
                $cart->product_variant_id = $product_variant_details->id;
                $cart->product_price = $product_price;
              //  $cart->product_price = $product_variant_details->variant_discounted_price;
                $cart->user_id = $user_id;
                $cart->quantity = $quantity;
                $cart->total_price =  $total_price;
                $cart->offer_price =  $offer_price;
                $cart->order_type = 1;
                $cart->status = 1;
                $cart->save();
            }
            $totalCount = \DB::table('cart')->where('user_id',$user_id)->where('status','1')->count();
            return response()->json(['success' => 'true','cart_count'=>@$totalCount?:0]);
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
