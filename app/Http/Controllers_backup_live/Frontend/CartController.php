<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Validation\Rule;
use App\Models\Cart;
use App\Models\FavoriteProduct;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\ProductVariants;
use App\Models\Setting;
use App\Models\Transactions;
use App\Models\UsersPayments;
use Auth;
use Carbon\Carbon;
use DB;
Use Helper;
use Mail;
use Redirect;
use Session;
use Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Bogo;
use App\Models\Offers;



class CartController extends Controller
{
    //
    public function __construct()
    {
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
        
    }

    public function Cart(Request $request)
    {
        $user_id = $this->user_id;
        $setting = $this->setting;        
        
        $cart_info = Session::get('cart_info');
        //When user not login
        if($cart_info!="" && $user_id==""){
            $variantIds = array();
            foreach($cart_info as $result){
                foreach($result as $keys => $value){
                    $variantIds[] = $keys;
                }               
            }  
        }else{
             //When user login
            $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $user_id)->groupBy('user_id')->where('status', 1)->first();
            
            if(!empty($cartData)){
                $variantIds = explode(',', $cartData->variantIds);
            }else{
            }

        }
        $productData = "";
        if(!empty($variantIds)){               
            $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id',$variantIds)->get(); 
        }

        /*-- Top selling product start--*/
        $top_product_count = Order::getTopSellingProduct();
        $product_id = array();
        foreach($top_product_count as $result ){
            $product_id[] = $result->product_id;
        }
        $top_products = Product::activeProductsBasedOnRelations()->whereIn('product.id',$product_id)->limit(6)->get();
        /*-- Top selling product end--*/
        $recent_view_ids = Session::get('recent_view_product_ids');  
        if($recent_view_ids!=""){
            $recent_view_ids = array_reverse($recent_view_ids); 
        }
        
        $recent_view_products = "";
        if($recent_view_ids){
            $recent_view_products = Product::activeProductsBasedOnRelations()->whereIn('product.id',$recent_view_ids)->limit(6)->get();
        } 

        // Bog status for top selling and recently viewed products
         $today = Carbon::now()->toDateString();

         // Top selling Product Bogo

        if($top_products)
        {
             foreach ($top_products as $top) {
             $bogo = Bogo::where('status', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->where(function ($query) use ($top) {
                    $query->where('product_id', $top->id)
                        ->orWhere(function ($q) use ($top) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $top->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($top) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $top->category_id);
                        });

                    if (!empty($top->brand_id)) {
                        $query->orWhere('brand_id', $top->brand_id);
                    }
                })
                ->first();

                $top->bogo_status = $bogo ? true : false; 

                // offer check
                    $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($top) {
                    $query->where('product_id', $top->id)
                        ->orWhere(function ($q) use ($top) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $top->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($top) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $top->category_id);
                        });

                    if (!empty($top->brand_id)) {
                        $query->orWhere('brand_id', $top->brand_id);
                    }
                })
                ->first();

                if ($offer) {
                    $top->offer_status = true;
                    $top->discount_amount = $offer->dis_amount;
                    $top->offer_type = $offer->offer_type;
                } else {
                    $top->offer_status = false;
                    $top->discount_amount = 0;
                    $top->offer_type = null;
                }
            }
        }

        // recent view product bogo
        if($recent_view_products)
        {
            foreach ($recent_view_products as $recent) {
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
        
        return view("frontend.cart.cart-list",compact('productData','user_id','top_products','recent_view_products'));
    }

    public function productCartRemove(Request $request){
        $user_id = $this->user_id;
        $get_cart_array = Session::get('cart_info');
        $variant_id = $request->variant_id;    

        if($user_id){            
            $cart_id = $request->get('cart_id');
            $removeItem = Cart::where('user_id', $user_id)->where('product_variant_id',$variant_id)->delete();
        }else{            
            $new_cart_array = array();        
            foreach($get_cart_array as $key => $variant_array){    
                if(array_key_exists($variant_id,$variant_array)){ 
                    unset($variant_array[$variant_id]);
                }
                //When item variant is one and remove it then  blank array is generation s this reason we are using below condition.
                if(!empty($variant_array)){
                    $new_cart_array[$key] = $variant_array;
                }               
            }
            Session::put('cart_info',$new_cart_array);
        }
        
        $buyNow = Session::get('buy_now_info');
        if (!empty($buyNow) && isset($buyNow['variantId']) && $buyNow['variantId'] == $variant_id) {
            Session::forget('buy_now_info');
        }

        return true;
    }

    // public function productCartIncrement(Request $request){
    //     $product_id = base64_decode($request->product_id);
    //     $quantity = $request->quantity;
    //     $variant_id = $request->variantId;   
    //     $product_variant = ProductVariants::where('id',$variant_id)->first();
    //     if($product_variant->available_qty < $quantity){
    //         return false;
    //     }
    //     $user_id = $this->user_id;
    //     $is_product_discount[] =  false;
    //     ///die;
    //     if(Auth::guard('user')->user()==""){
    //         $get_cart_array = Session::get('cart_info');                     
    //         /*---session have cart values if product is exits.--*/
    //         if(array_key_exists($product_id,$get_cart_array)){
    //             foreach($get_cart_array as $key => $variant_array){     
    //                 if($key==$product_id && array_key_exists($variant_id,$variant_array)){                       
    //                     $new_array = array($product_id=>array($variant_id=>$quantity));
    //                     $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
    //                     $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
    //                     $result = array_replace_recursive($get_cart_array, $new_array);                      
    //                     Session::put('cart_info',$result);
    //                 }else{
    //                     $new_array = array($product_id=>array($variant_id=>$quantity));
    //                     $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
    //                     $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
    //                     $result = array_replace_recursive($get_cart_array, $new_array);                      
    //                     Session::put('cart_info',$result);
    //                 }
    //             }
    //         }else{
    //             /*---combine if already product in cart and add new cart product in sesstion.--*/
    //             $new_array = array($product_id=>array($variant_id=>$quantity));
    //             $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
    //             $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
    //             $result = array_replace_recursive($get_cart_array, $new_array);
    //             Session::put('cart_info',$result);          
    //         }             

    //         $cart_info = Session::get('cart_info');
    //         if($cart_info!="" && $user_id==""){
    //             $variantIds = array();
    //             foreach($cart_info as $result){
    //                 foreach($result as $keys => $value){
    //                     $variantIds[] = $keys;
    //                 }               
    //             }  
    //             $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id',$variantIds)->get();
    //             $original_price =0;
    //             $total_discount_price =0; 
    //             if($cart_info!="" && Auth::guard('user')->user()==''){
    //                 foreach($productData as $result){
    //                     $org_price = @$result->variant_price ? ($result->variant_price * Helper::getCartQuantity($result->id)): 0;
    //                     $original_price += $org_price;
                       
    //                     if($result->variant_discounted_price !='' && $result->variant_discounted_price != 0.00){                      
    //                         $discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getCartQuantity($result->id)) : 0; 
    //                         $total_discount_price += ($org_price - $discount_price);
    //                         $is_product_discount[] = true;
    //                     }                      
    //                 }
    //             } 
    //         }   
            
    //     }else{
    //         $variant_info = ProductVariants::getProductDetalsBasedOnVariant()->where('id',$variant_id)->first();
    //         if($variant_info){
    //             $offer_price = $variant_info->variant_discounted_price;
    //             $varint_pro_price = $variant_info->variant_price;

    //             if($variant_info->variant_discounted_price !='' && $variant_info->variant_discounted_price != 0.00){                       
    //                 $tcart_price = @$variant_info->variant_discounted_price ? ($variant_info->variant_discounted_price * $quantity) : 0; 
    //             }else{                   
    //                 $tcart_price = @$variant_info->variant_price ? ($variant_info->variant_price * $quantity) : 0; 
    //             }

    //             $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $product_id)->where('product_variant_id', $variant_id)->update(array(                           
    //                 'quantity' => $quantity,
    //                 'total_price' => $tcart_price,
    //                 'product_price' => $varint_pro_price,
    //                 'offer_price' => $offer_price,
    //             ));
    //         }
            
            
    //         $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))->where('user_id', $user_id)->groupBy('user_id')->where('status', 1)->first();
    //         $variantIds = explode(',', $cartData->variantIds);
    //         $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id',$variantIds)->get();
    //        // dd($productData );
    //         $original_price =0;
    //         $total_discount_price =0;             
    //         foreach($productData as $result){
    //             $org_price = @$result->variant_price ? ($result->variant_price * Helper::getUserCartQuantity($result->id,$user_id)): 0;
    //             $original_price += $org_price;
    //             if($result->variant_discounted_price !='' && $result->variant_discounted_price != 0.00){
    //                 $discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getUserCartQuantity($result->id,$user_id)) : 0; 
    //                 $is_product_discount[] = true;
    //                 $total_discount_price += ($org_price - $discount_price);
    //             }
                
    //         } 
    //     }
    //     $total_products_price = $original_price;

    //     if(in_array(true, $is_product_discount)){
    //         $final_amount =  ($total_products_price -  $total_discount_price);
    //     }else{
    //         $final_amount =  $total_products_price;
    //     }
        
    //     $tax_amount = ( Helper::Settings('tax') / 100) * $final_amount;
    //     $tax_amount = round($tax_amount, 2);
    //     $total_amount = $final_amount + $tax_amount;
    //     return response()->json(['success' => 'true','total_products_price'=>$total_products_price,'total_discount_price'=>$total_discount_price,'tax_amount'=>$tax_amount,'total_amount'=>$total_amount]);
    // }

    public function productCartIncrement(Request $request){
        $product_id = base64_decode($request->product_id);
        $quantity = $request->quantity;
        $variant_id = $request->variantId;
        $offer_status = (int) $request->offer_status; 
        $product_variant = ProductVariants::where('id',$variant_id)->first();
        
        if($product_variant->available_qty < $quantity){
            return false;
        }

        $user_id = $this->user_id;
        $is_product_discount[] = false;


        // ✅ Get current Offer
        $today = Carbon::now()->toDateString();
        $currentOffer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->first(); 

        $currentOfferType = null;
        $currentOfferAmount = null;

        if($currentOffer){
            $currentOfferType = $currentOffer->offer_type;
            $currentOfferAmount = $currentOffer->dis_amount;
        }

        if(Auth::guard('user')->user() == ""){
            // ✅ GUEST USER - SESSION CART
            $get_cart_array = Session::get('cart_info');      

            $new_item = ['quantity' => $quantity]; 

            if ($offer_status) {
                $new_item['offer_type'] = $currentOfferType;
                $new_item['discount_amount'] = $currentOfferAmount;
                $new_item['is_offer'] = $offer_status;
            }

            if(array_key_exists($product_id, $get_cart_array)){
                foreach($get_cart_array as $key => $variant_array){     
                    if($key == $product_id && array_key_exists($variant_id, $variant_array)){                       
                        $new_array = array($product_id => array($variant_id => $new_item));
                        $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
                        $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
                        $result = array_replace_recursive($get_cart_array, $new_array);                      
                        Session::put('cart_info', $result);
                    } else {
                        $new_array = array($product_id => array($variant_id => $new_item));
                        $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
                        $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
                        $result = array_replace_recursive($get_cart_array, $new_array);                      
                        Session::put('cart_info', $result);
                    }
                }
            } else {
                $new_array = array($product_id => array($variant_id => $new_item));
                $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
                $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array); 
                $result = array_replace_recursive($get_cart_array, $new_array);
                Session::put('cart_info', $result);          
            }             

            // Total calc for guest cart
            $cart_info = Session::get('cart_info');
            if($cart_info != "" && $user_id == ""){
                $variantIds = array();
                foreach($cart_info as $result){
                    foreach($result as $keys => $value){
                        $variantIds[] = $keys;
                    }               
                }  
                $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();
                $original_price = 0;
                $total_discount_price = 0; 

                foreach($productData as $result){
                    $is_offer=Helper::getCartOfferStatus($result->id);
                    $discount_amount=Helper::getCartDiscountAmount($result->id);
                    $offer_type=Helper::getCartOfferType($result->id);
                    $qty = Helper::getCartQuantity($result->id);

                    $org_price = @$result->variant_price ? ($result->variant_price  * $qty) : 0;
                    $original_price += $org_price;
                    $discount_price = $org_price;

                     if ($is_offer) {
                        if ($offer_type == 'flat') {
                            // Multiply flat discount per item
                            $discount_price = max(0, $org_price - ($discount_amount * $qty));
                        } elseif ($offer_type == 'percentage') {
                            $discount_price = max(0, $org_price - ($org_price * $discount_amount / 100));
                        }
                        $is_product_discount[] = true;
                    } elseif ($result->variant_discounted_price && $result->variant_discounted_price != 0) {
                        $discount_price = $result->variant_discounted_price * $qty;
                        $is_product_discount[] = true;
                    }

                    $total_discount_price += ($org_price - $discount_price);
                    
                }
            }   
            
        } else {
            // ✅ LOGGED IN USER - DATABASE CART
            $variant_info = ProductVariants::getProductDetalsBasedOnVariant()->where('id', $variant_id)->first();

            if($variant_info){
                $offer_price = $variant_info->variant_discounted_price;
                $varint_pro_price = $variant_info->variant_price;

                if($variant_info->variant_discounted_price != '' && $variant_info->variant_discounted_price != 0.00){                       
                    $tcart_price = @$variant_info->variant_discounted_price ? ($variant_info->variant_discounted_price * $quantity) : 0; 
                } else {                   
                    $tcart_price = @$variant_info->variant_price ? ($variant_info->variant_price * $quantity) : 0; 
                }

                // ✅ Persist offer info while updating
                $updateData = [
                    'quantity' => $quantity,
                    'total_price' => $tcart_price,
                    'product_price' => $varint_pro_price,
                    'offer_price' => $offer_price,
                ];

                if ($offer_status) {
                    $updateData['offer_type'] = $currentOfferType;
                    $updateData['discount_amount'] = $currentOfferAmount;
                    $updateData['is_offer'] = $offer_status;
                } else {
                    $updateData['offer_type'] = null;
                    $updateData['discount_amount'] = null;
                    $updateData['is_offer'] = 0;
                }

                Cart::where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->where('product_variant_id', $variant_id)
                    ->update($updateData); 
            }

            // Total calc for DB cart
            $cartData = Cart::select(DB::raw('group_concat(product_variant_id) as variantIds'))
                ->where('user_id', $user_id)
                ->groupBy('user_id')
                ->where('status', 1)
                ->first();

            $variantIds = explode(',', $cartData->variantIds);
            $productData = ProductVariants::getProductDetalsBasedOnVariant()->with('cart')->whereIn('id', $variantIds)->get();

            $original_price = 0;
            $total_discount_price = 0;             
            foreach($productData as $result){
                $offer_cart = $result->cart->where('user_id', $user_id)->sortByDesc('id')->first();
                $is_offer = $offer_cart ? $offer_cart->is_offer : 0;
                $discount_amount = $offer_cart ? $offer_cart->discount_amount : null;
                $offer_type = $offer_cart ? $offer_cart->offer_type : null;
                $qty = Helper::getUserCartQuantity($result->id, $user_id);

                $org_price = @$result->variant_price ? ($result->variant_price * $qty) : 0;
                $original_price += $org_price;
                $discount_price = $org_price;

                if ($is_offer) {
                    if ($offer_type == 'flat') {
                        // Multiply flat discount per item
                        $discount_price = max(0, $org_price - ($discount_amount * $qty));
                    } elseif ($offer_type == 'percentage') {
                        $discount_price = max(0, $org_price - ($org_price * $discount_amount / 100));
                    }
                    $is_product_discount[] = true;
                } elseif ($result->variant_discounted_price && $result->variant_discounted_price != 0) {
                    $discount_price = $result->variant_discounted_price * $qty;
                    $is_product_discount[] = true;
                }

                $total_discount_price += ($org_price - $discount_price);

            } 
        }

        $total_products_price = $original_price;

        if(in_array(true, $is_product_discount)){
            $final_amount =  ($total_products_price - $total_discount_price);
        } else {
            $final_amount =  $total_products_price;
        }

        $tax_amount = (Helper::Settings('tax') / 100) * $final_amount;
        $tax_amount = round($tax_amount, 2);
        $total_amount = $final_amount + $tax_amount;

        return response()->json([
            'success' => 'true',
            'total_products_price' => $total_products_price,
            'total_discount_price' => $total_discount_price,
            'tax_amount' => $tax_amount,
            'total_amount' => $total_amount
        ]);
    }


    public function buyNowSession(Request $request)
    {
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

        // resetting
        Session::forget('checkout_in_progress');
        Session::forget('buy_now_info');

        $bogo_status = (int) $request->bogo_status;
        $offer_status = (int) $request->offer_status;


        $data = [
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'variantId' => $request->variantId,
            'is_bogo' => $bogo_status,
        ];
  
        if (!$bogo_status && $offer_status) {
            $data['offer_type'] = $currentOfferType;
            $data['discount_amount'] = $currentOfferAmount;
            $data['is_offer'] = $offer_status;
        }

        Session::put('buy_now_info', $data);

        return response()->json(['success' => true]);
    }

}
