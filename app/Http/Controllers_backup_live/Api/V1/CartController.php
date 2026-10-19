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
use App\Models\Community;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderTracking;
use App\Models\OrderDetails;
use App\Models\Transactions;
use App\Models\UsersPayments;
use App\Models\Notification;
use App\Models\ProductVariants; 
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

class CartController extends Controller
{
    //
    
    // public function cartManager(Request $request)
    // {
    //     logger()->info("+++++++++++++++++++++++++++CartController - cartManager+++++++++++++++++++++++");
    //     logger()->info($request->all());
        
    //     logger()->info("-----------------------------------------");
        
        
        
    //     $result = [];
    //     $finalArr = [];
    //     $validator = \Validator::make($request->all(), [
    //         'type' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'code' => strval(0),
    //             'error'=>$validator->messages(),
    //             'data' => null
    //         ], 200);
    //     }
    //     $type= $request->type;
    //     //1 for add product
    //     if($type==1){
    //         $validator = \Validator::make($request->all(), [
    //             'uniqid' => 'required',
    //             'token' => 'required',
    //             'product_id' => 'required',
    //             'quantity' => 'required',
    //             'variant_id'=>'required',                
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => strval(0),
    //                 'error'=>$validator->messages(),
    //                 'data' => null
    //             ], 200);
    //         }

    //         $product_id = $request->product_id;
    //         $variant_id = $request->variant_id;
    //         $quantity = $request->quantity;
    //         $cart_type = $request->cart_type;

    //         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);            
    //         if ($response['code'] != 1) {
    //             $mainResult   =   $response;
    //             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //         }
    //            /*-- Get product variant details---*/
    //         $product_variant_details = DB::table('product')->leftjoin('product_variants', 'product_variants.product_id', '=', 'product.id')->where('product.id', $product_id)->where('product_variants.id', $variant_id)->select('product_variants.*')->first();
            
    //         if (empty($product_variant_details)) {
    //             $result['code']     =  strval(0);
    //             $result['message']  =   'something_went_wrong';
    //             $result['result']       =   [];

    //             $mainResult   =   $result;
    //             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //         }

            
            
    //         $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
    //         $user_id = $userData->id;
         
    //         // if($product_variant_details->variant_discounted_price !='' && $product_variant_details->variant_discounted_price != 0.00){   
    //         //     $varint_pro_price = $product_variant_details->variant_discounted_price;
    //         //     $tcart_price = @$product_variant_details->variant_discounted_price ? ($product_variant_details->variant_discounted_price * $quantity) : 0; 
    //         // }else{
    //         //     $varint_pro_price = @$product_variant_details->variant_price?:0;
    //         //     $tcart_price = @$product_variant_details->variant_price ? ($product_variant_details->variant_price * $quantity) : 0; 
    //         // }  

    //         /*--- Check user request data is in cart or not ---*/
    //         $cartData = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $user_id)->where('cart.product_id', $product_id)->where('product_variant_id', $variant_id)->where('cart.status', 1)->select('cart.*')->first();
    //         //cart type if product page add to cart
    //         if(!empty($cartData) && $cart_type==1){
    //             $quantity = $cartData->quantity + $quantity;
    //         }

    //         if($product_variant_details->available_qty < $quantity){
    //             $result['code']     =  strval(1);
    //             $result['message']  =  'product_quantity_is_not_availiable';   
    //             $mainResult   =  $result;
    //             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //         }

    //         $offer_price = $product_variant_details->variant_discounted_price;
    //         $varint_pro_price = $product_variant_details->variant_price;

    //         if($product_variant_details->variant_discounted_price !='' && $product_variant_details->variant_discounted_price != 0.00){                       
    //             $tcart_price = @$product_variant_details->variant_discounted_price ? ($product_variant_details->variant_discounted_price * $quantity) : 0; 
    //         }else{                   
    //             $tcart_price = @$product_variant_details->variant_price ? ($product_variant_details->variant_price * $quantity) : 0; 
    //         }


    //         if (!empty($cartData)) {
    //             $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $product_id)->where('product_variant_id', $variant_id)->update(array(
    //                 'product_price' => $varint_pro_price,        
    //                 'quantity' => $quantity,
    //                 'total_price' => $tcart_price,
    //                 'offer_price' => $offer_price,
    //             ));
    //             $result['code']     =  strval(1);
    //             $result['message']  =   'cart_update_success';    
    //             $mainResult   =   $result;
    //             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

    //         } else {
    //             $uniqid = uniqid();
    //             $cart = new Cart();
    //             $cart->uniqid = $uniqid;
    //             $cart->product_id = $product_id;
    //             $cart->product_variant_id = $product_variant_details->id;
    //             $cart->product_price = $varint_pro_price;
    //             $cart->user_id = $user_id;
    //             $cart->quantity = $quantity;
    //             $cart->total_price =  $tcart_price;
    //             $cart->offer_price =  $offer_price;
    //             $cart->order_type =  1;
    //             $cart->status = 1;
    //             $cart->save();

    //             $result['code']     =  strval(1);
    //             $result['message']  =   'added_cart_successfully';           
        
    //             $mainResult   =   $result;
    //             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //         }
    //         //$totalCount = \DB::table('cart')->where('user_id',$user_id)->where('status','1')->count();
    //         // $result['code']     =  strval(1);
    //         // $result['message']  =   'cart_updated_successfully';   
    //         // $mainResult   =   $result;
    //         // return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

    //     }else{
            
    //        // 2 for remove product
    //         $validator = \Validator::make($request->all(), [
    //             'cart_id' => 'required',              
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'code' => strval(0),
    //                 'error'=>$validator->messages(),
    //                 'data' => null
    //             ], 200);
    //         }

    //         $updatepsw = Cart::where('id', $request->cart_id)->update(array( 'status' => 2));

    //         $result['code']     =  strval(1);
    //         $result['message']  =   'remove_cart_product_success';            
    
    //         $mainResult   =   $result;
    //         return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

    //     }
    // }

     public function cartManager(Request $request)
    {
        logger()->info("-------------------Request Data Cart manager----------------------");
        logger()->info($request->all());
        
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }
        $type= $request->type;
        //1 for add product
        if($type==1){
            $validator = \Validator::make($request->all(), [
                'uniqid' => 'required',
                'token' => 'required',
                'product_id' => 'required',
                'quantity' => 'required',
                'variant_id'=>'required',                
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error'=>$validator->messages(),
                    'data' => null
                ], 200);
            }

            $product_id = $request->product_id;
            $variant_id = $request->variant_id;
            $cart_type = $request->cart_type;
            // $quantity = $request->quantity;
            $quantity = (int) $request->quantity;
            // $bogo_status = (int) $request->bogo_status;
            // $offer_status = (int) $request->offer_status;
            $offer_status = (int) filter_var($request->offer_status, FILTER_VALIDATE_BOOLEAN);
            $bogo_status  = (int) filter_var($request->bogo_status, FILTER_VALIDATE_BOOLEAN);

            logger()->info('bogo_status: ' . json_encode($bogo_status));
            logger()->info('offer_status: ' . json_encode($offer_status));
            logger()->info('quantity: ' . json_encode($quantity));


       
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

            $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);            
            if ($response['code'] != 1) {
                $mainResult   =   $response;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
               /*-- Get product variant details---*/
            $product_variant_details = DB::table('product')->leftjoin('product_variants', 'product_variants.product_id', '=', 'product.id')->where('product.id', $product_id)->where('product_variants.id', $variant_id)->select('product_variants.*')->first();
            
            if (empty($product_variant_details)) {
                $result['code']     =  strval(0);
                $result['message']  =   'something_went_wrong';
                $result['result']       =   [];

                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }

            
            
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $user_id = $userData->id;

            // if($product_variant_details->variant_discounted_price !='' && $product_variant_details->variant_discounted_price != 0.00){   
            //     $varint_pro_price = $product_variant_details->variant_discounted_price;
            //     $tcart_price = @$product_variant_details->variant_discounted_price ? ($product_variant_details->variant_discounted_price * $quantity) : 0; 
            // }else{
            //     $varint_pro_price = @$product_variant_details->variant_price?:0;
            //     $tcart_price = @$product_variant_details->variant_price ? ($product_variant_details->variant_price * $quantity) : 0; 
            // }  

            /*--- Check user request data is in cart or not ---*/
            $cartData = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $user_id)->where('cart.product_id', $product_id)->where('product_variant_id', $variant_id)->where('cart.status', 1)->select('cart.*')->first();
            //cart type if product page add to cart
            if(!empty($cartData) && $cart_type==1){
                $quantity = $cartData->quantity + $quantity;
            }

            if($product_variant_details->available_qty < $quantity){
                $result['code']     =  strval(1);
                $result['message']  =  'product_quantity_is_not_availiable';   
                $mainResult   =  $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }

            $offer_price = $product_variant_details->variant_discounted_price;
            $varint_pro_price = $product_variant_details->variant_price;

            if($product_variant_details->variant_discounted_price !='' && $product_variant_details->variant_discounted_price != 0.00){                       
                $tcart_price = @$product_variant_details->variant_discounted_price ? ($product_variant_details->variant_discounted_price * $quantity) : 0; 
            }else{                   
                $tcart_price = @$product_variant_details->variant_price ? ($product_variant_details->variant_price * $quantity) : 0; 
            }


            if (!empty($cartData)) {
                $updateData = [
                    'product_price' => $varint_pro_price,
                    'offer_price' => $offer_price,
                    'quantity' => $quantity,
                    'total_price' => $tcart_price,
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

                $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $product_id)->where('product_variant_id', $variant_id)->update($updateData);
                $result['code']     =  strval(1);
                $result['message']  =   'cart_update_success';    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

            } else {
                $uniqid = uniqid();
                $cart = new Cart();
                $cart->uniqid = $uniqid;
                $cart->product_id = $product_id;
                $cart->product_variant_id = $product_variant_details->id;
                $cart->product_price = $varint_pro_price;
                $cart->user_id = $user_id;
                $cart->total_price =  $tcart_price;
                $cart->offer_price =  $offer_price;
                $cart->order_type =  1;
                $cart->quantity = $quantity;
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

                $result['code']     =  strval(1);
                $result['message']  =   'added_cart_successfully';           
        
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
            //$totalCount = \DB::table('cart')->where('user_id',$user_id)->where('status','1')->count();
            // $result['code']     =  strval(1);
            // $result['message']  =   'cart_updated_successfully';   
            // $mainResult   =   $result;
            // return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

        }else{
            
           // 2 for remove product
            $validator = \Validator::make($request->all(), [
                'cart_id' => 'required',              
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => strval(0),
                    'error'=>$validator->messages(),
                    'data' => null
                ], 200);
            }

            $updatepsw = Cart::where('id', $request->cart_id)->update(array( 'status' => 2));

            $result['code']     =  strval(1);
            $result['message']  =   'remove_cart_product_success';            
    
            $mainResult   =   $result;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));

        }
    }

    public function incrementCart(Request $request)
    {
        
        logger()->info("+++++++++++++++++++++++++++CartController - incrementCart+++++++++++++++++++++++");
        logger()->info($request->all());
        
        logger()->info("-----------------------------------------");
        
        
        
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $product_id = $request->product_id;
        // $quantity = $request->quantity;
        $quantity = (int) $request->quantity;
        $offer_status = (int) filter_var($request->offer_status, FILTER_VALIDATE_BOOLEAN);

        logger()->info('offer_status: ' . json_encode($offer_status));

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

         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

         if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $user_id = $userData->id;

            $cartData = DB::table('cart')->where('user_id',$user_id)->where('product_id',$product_id)->where('status',1)->first();

            logger()->info('cartData: ' . json_encode($cartData));

            $productData = DB::table('product')->where('id',$product_id)->where('status',1)->first();

            $quantity = $cartData->quantity+1;
            
            // $offer_price = $quantity*(isset($productData->discount_price) ? $productData->discount_price : '0');
            // $product_price_data = $quantity*(isset($productData->retail_price) ? $productData->retail_price : '0');

            $offer_price = isset($cartData->offer_price) ? $cartData->offer_price : '0';
            $product_price_data =isset($cartData->product_price) ? $cartData->product_price : '0';

            if ($offer_price==0) {
                $total_price = $product_price_data * $quantity;
            }else{
                $total_price = $offer_price * $quantity;
            }

            // $updatepsw = Cart::where('user_id', $user_id)->where('product_id',$product_id)->update(array(
            //         'product_price' => $product_price_data,
            //         'offer_price' => $offer_price,
            //         'quantity' => $quantity,
            //         'total_price' => $total_price,
            //     ));
            
            // Persist offer info while updating
            $updateData = [
                'quantity' => $quantity,
                'total_price' => $total_price,
                'product_price' => $product_price_data,
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

            $updatepsw = Cart::where('user_id', $user_id)->where('product_id',$product_id)->update($updateData);
            logger()->info('updatepsw increment: ' . json_encode($updatepsw));


            $result['code']     =  strval(1);
                $result['message']  =   'cart_updated_successfully';
                // $result['result'][]   = [];
        
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

    public function decrementCart(Request $request)
    {
        
        logger()->info("+++++++++++++++++++++++++++CartController - decrementCart+++++++++++++++++++++++");
        logger()->info($request->all());
        
        logger()->info("-----------------------------------------");
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $product_id = $request->product_id;
        // $quantity = (int) $request->quantity;
        $quantity = (int) $request->quantity;
        $offer_status = (int) filter_var($request->offer_status, FILTER_VALIDATE_BOOLEAN);

        logger()->info('offer_status: ' . json_encode($offer_status));

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


         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

         if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $user_id = $userData->id;

            $cartData = DB::table('cart')->where('user_id',$user_id)->where('product_id',$product_id)->where('status',1)->first();
            $productData = DB::table('product')->where('id',$product_id)->where('status',1)->first();
            $quantity = $cartData->quantity-1;

            // $offer_price = $quantity*(isset($productData->discount_price) ? $productData->discount_price : '0');
            // $product_price_data = $quantity*(isset($productData->retail_price) ? $productData->retail_price : '0');

            $offer_price = isset($cartData->offer_price) ? $cartData->offer_price : '0';
            $product_price_data =isset($cartData->product_price) ? $cartData->product_price : '0';

            if ($offer_price==0) {
                $total_price = $product_price_data * $quantity;
            }else{
                $total_price = $offer_price * $quantity;
            }

            if ($cartData->quantity ==1) {
            $updatepsw = Cart::where('user_id', $user_id)->where('product_id',$product_id)->update(array(
                   'status' => 2,
                ));
            }else{

                // $updatepsw = Cart::where('user_id', $user_id)->where('product_id',$product_id)->update(array(
                //     'product_price' => $product_price_data,
                //     'offer_price' => $offer_price,
                //     'quantity' => $quantity,
                //     'total_price' => $total_price,
                // ));

                // Persist offer info while updating
                $updateData = [
                    'quantity' => $quantity,
                    'total_price' => $total_price,
                    'product_price' => $product_price_data,
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

                $updatepsw = Cart::where('user_id', $user_id)->where('product_id',$product_id)->update($updateData);
                logger()->info('updatepsw decrement: ' . json_encode($updatepsw));

            }

            $result['code']     =  strval(1);
                $result['message']  =   'cart_updated_successfully';
                // $result['result'][]   = [];
        
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

    public function placeOrder(Request $request)
    {
        
        
        logger()->info("+++++++++++++++++++++++++++CartController - placeOrder+++++++++++++++++++++++");
        logger()->info($request->all());
        
        logger()->info("-----------------------------------------");

        
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

        $product_id = $request->product_id;
        $quantity = $request->quantity;

         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

         if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();

            $userAddress = DB::table('user_address')->where('status',1)->where('user_id',$userData->id)->where('is_selected_address_id',1)->first();

            $cartData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->leftJoin('main_users','main_users.id','=','product.supplier_id')->where('cart.status',1)->where('product.status',1)->where('product.is_admin_approve',1)->where('cart.user_id',$userData->id)->select('cart.*','product.supplier_id','product.status as product_status','product.product_name','main_users.store_name','main_users.id as store_id','product.product_image');

            $cartData = $cartData->get();

            $cartTotalPrice = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.user_id',$userData->id)->where('cart.status',1)->where('product.status',1)->where('product.is_admin_approve',1)->sum('cart.product_price');

            $cartDiscountPrice = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.user_id',$userData->id)->where('cart.status',1)->where('product.status',1)->where('product.is_admin_approve',1)->sum('cart.offer_price');
            // echo "<pre>";print_r($userAddress);exit();
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

            $productArr = [];

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
                $productArr[] = $cartList;
            }

            $mainArr['product_list'] = $productArr;

            $mainArr['name'] = strval(@$userAddress->name);
            $mainArr['address'] = strval(@$userAddress->address);
            $mainArr['country'] = strval(@$userAddress->country);
            $mainArr['state'] = strval(@$userAddress->state);
            $mainArr['city'] = strval(@$userAddress->city);
            $mainArr['zip_code'] = strval(@$userAddress->zip_code);

            $mainArr['cartTotalPrice'] = strval(@$cartTotalPrice);
            $mainArr['totalCartAmount'] = strval(@$totalCartAmount);
            $mainArr['totalDicountPrice'] = strval(@$totalDicountPrice);

            // echo "<pre>";print_r($cartData);exit();

                $result['code']     =  strval(1);
                $result['message']  =   'success';
                $result['result'][]   = $mainArr;
        
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

    public function orderSuccessfully(Request $request)
    {
        
        logger()->info("+++++++++++++++++++++++++++CartController - orderSuccessfully+++++++++++++++++++++++");
        logger()->info($request->all());
        
        logger()->info("-----------------------------------------");
        
        
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'transaction_id' => 'required',
            // 'card_name' => 'required',
            // 'card_number' => 'required',
            // 'exp_month' => 'required',
            // 'exp_year' => 'required',
            // 'cvv' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $product_id = $request->product_id;
        $quantity = $request->quantity;

         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

         if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $user_id = $userData->id;

            $userAddress = DB::table('user_address')->where('status',1)->where('user_id',$user_id)->where('is_selected_address_id',1)->first();

            $cartData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.status',1)->where('cart.user_id',$user_id)->where('product.status',1)->select('cart.*','product.supplier_id','product.status as product_status');
            $cartData = $cartData->get();

            $cartTotalPrice = DB::table('cart')->where('user_id',$user_id)->where('status',1)->sum('product_price');

            $cartDiscountPrice = DB::table('cart')->where('user_id',$user_id)->where('status',1)->sum('offer_price');
            // echo "<pre>";print_r($cartData);exit();
            $totalDicountPrice = @$cartTotalPrice - @$cartDiscountPrice;

            $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;
            // echo "<pre>";print_r($cartData);exit();
            
            $cartSupplierData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.status',1)->where('product.status',1)->where('cart.user_id',$user_id)->select('cart.*','product.supplier_id');

            $cartSupplierData = $cartSupplierData->groupby('product.supplier_id')->get();

            $cartSupplierData_new = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.status',1)->where('product.status',1)->where('cart.user_id',$user_id)->select('cart.*','product.supplier_id');

            $cartSupplierData_new = $cartSupplierData_new->groupby('product.supplier_id')->first();

            $store_name = DB::table('main_users')->where('id',$cartSupplierData_new->supplier_id)->first();

            foreach($cartSupplierData as $c)
            {
                $store_name_new = DB::table('main_users')->where('id',$c->supplier_id)->first();

                if(!$store_name_new)
                {
                    $result['code']     =  strval(0);
                    $result['message']  =   'something_went_wrong';
                    $result['result']       =   [];
                        
                    $mainResult   =   $result;
                    return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
            }

            // $secret_key = env('STRIPE_SECRET_KEY');
            $stripe = \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            $response="";


            $cartDataSupplier = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.status',1)->where('product.status',1)->where('cart.user_id',$user_id)->select('product.supplier_id')->groupby('product.supplier_id')->get();

            if (!empty($request->transaction_id)) {

                 foreach ($cartDataSupplier as $cartSupplier) {
                    $uniqid = uniqid();
        $transaction_id = uniqid();

         $cartTotalPrice = DB::table('cart')->where('user_id',$user_id)->where('supplier_id',$cartSupplier->supplier_id)->where('status',1)->sum('product_price');

        $cartDiscountPrice = DB::table('cart')->where('user_id',$user_id)->where('supplier_id',$cartSupplier->supplier_id)->where('status',1)->sum('offer_price');
        // echo "<pre>";print_r($cartData);exit();
        $totalDicountPrice = @$cartTotalPrice - @$cartDiscountPrice;

        $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;
         

        $order = new Order();
        $order->uniqid = $uniqid;
        $order->transaction_id = $request->transaction_id;
        $order->supplier_id = $cartSupplier->supplier_id;
        $order->user_id = $user_id;
        $order->delivery_address_id = $userAddress->id;
        $order->order_type = 1;
        $order->total_amount = $cartTotalPrice;
        $order->payable_amount = $totalCartAmount;
        $order->order_date = date('Y-m-d H:i:s');
        $order->order_time = date('Y-m-d H:i:s');
        $order->order_status = 0;
        $order->status = 1;

        $order->save();

        $number = str_pad($order->id,6,'0',STR_PAD_LEFT);
        $order_id = 'T25'.$number;

        $orderdataID[] = $order_id; 

        $updatepsw = Order::where('user_id', $user_id)->where('id',$order->id)->update(array(
                 'order_id' => @$order_id,
        ));

        $tracking_id = uniqid();

        $order_tracking = new OrderTracking();
        $order_tracking->order_id = $order->id;
        $order_tracking->uniqid = $tracking_id;
        $order_tracking->order_status = 0;
        $order_tracking->status = 1;

        $order_tracking->save();

        $cartData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.status',1)->where('product.status',1)->where('cart.user_id',$user_id)->where('product.supplier_id',$cartSupplier->supplier_id)->select('cart.*','product.supplier_id','product.status as product_status');
        $cartData = $cartData->get();

        foreach ($cartData as $cart) {
            
             $community = new Community();
            $community->user_id = $user_id;
            $community->product_id = $cart->product_id;
            $community->status = 1;

            $community->save();

            $order_detail = new OrderDetails(); 
            $order_detail->order_id = @$order->id;   
            $order_detail->product_id = @$cart->product_id;   
            $order_detail->quantity = @$cart->quantity;   
            $order_detail->supplier_id = @$cart->supplier_id;   
            $order_detail->status = 1;

            $order_detail->save(); 
        }

        $trans_no = md5(rand(1, 10) . microtime());

        $transactions = new Transactions();
        $transactions->trans_no = $trans_no;
        $transactions->user_id = $user_id;
        $transactions->supplier_id = @$cartSupplier->supplier_id;
        $transactions->order_id = @$order->id;
        $transactions->amount = $totalCartAmount;
        $transactions->transaction_date = date('Y-m-d H:i:s');
        $transactions->status = 1;

        $transactions->save();

        $user_payment = new UsersPayments();
        $user_payment->user_id = $user_id;
        $user_payment->supplier_id = @$cartSupplier->supplier_id;
        $user_payment->order_id = @$order->id;
        $user_payment->transaction_id = @$transactions->id;
        $user_payment->payment_mode = 1;
        $user_payment->status = 1;

        $user_payment->save();

        $updatepsw = Order::where('user_id', $user_id)->where('id',$order->id)->update(array(
                   'transaction_id' => @$transactions->id,
                ));
        //Online order confirmed order_id from store_name.
         $userData = DB::table('main_users')->where('id',$user_id)->first();
        $title = "Online Order Confirm";
        $message = "Online order confirmed ".$order_id." from ".@$store_name->store_name;
        $remember_token = "cyHePWLCmELqvi4nUSw-qE:APA91bEKD5z6rCn4wXOcfSQsl5Il69DIsoEE3-OmfH2-lfe4OlsPPB17CT0jCOuU9QcKxNcDX8IhZnwihNVvucOy45bOpltsKA608_xaJdO24vmgF6oa2QB8qG-E14Ak_krtpkabPb0a";
        $device_token = $userData->device_token;
        // echo "<pre>";print_r($device_token);exit();
        $device_type = 1;

        $notification = new Notification();

        $notification->sender_id = @$user_id;
        $notification->receiver_id = @$user_id;
        $notification->notification_type = 1;
        $notification->title = @$title;
        $notification->message = @$message;
        $notification->is_read = 0;
        $notification->save();

         $response = (new \Helper)->send_notification_FCM($remember_token, $title, $message, $device_type);
        $response = (new \Helper)->sendNotification($device_token, $title, $message, $device_type);

        }       
        
        $mainArr = [];
        $mainArr['order_id'] = $order->id;
        $mainArr['trans_no'] = $trans_no;
        $mainArr['store_name'] = $store_name->store_name;

        $updatepsw = Cart::where('user_id', $user_id)->update(array(
                   'status' => 2,
        ));


                $result['code']     =  strval(1);
                $result['message']  =   'success';
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

        }else{

                $result['code']     =  strval(0);
                $result['message']  =   'something_went_wrong';
                $result['result']       =   [];
                    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function pickUpOrder(Request $request)
    {
        
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            // 'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $product_id = $request->product_id;
        $quantity = $request->quantity;

         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

         if($post)
        {

            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $user_id = $userData->id;
            $order_type = 1;

            $updatepsw = Cart::where('user_id', $user_id)->update(array(
                   'order_type' => $order_type,
                ));

            $cartSupplierData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.status',1)->where('cart.user_id',$user_id)->select('cart.*','product.supplier_id');

            $cartSupplierData = $cartSupplierData->groupby('product.supplier_id')->first();

            $cartData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.status',1)->where('cart.user_id',$user_id)->select('cart.*','product.supplier_id');
            $cartData = $cartData->get();

            $cartStatusData = DB::table('cart')->where('cart.status',1)->where('cart.user_id',$user_id)->pluck('product_id');
            // echo "<pre>";print_r($cartStatusData);exit();
           
           $cartDatacheck = DB::table('product')->wherein('id',$cartStatusData)->where('status', '!=', 1);

           if($cartDatacheck->exists()) {
                $result['code']     =  strval(-13);
                $result['message']  =   'Some_of_products_from_your_cart_are_not_available_right_now_please_remove_them_and_try_again';
                $result['result']       =   [];
                    
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
           }

            $cartTotalPrice = DB::table('cart')->where('user_id',$user_id)->where('status',1)->sum('product_price');

            $cartDiscountPrice = DB::table('cart')->where('user_id',$user_id)->where('status',1)->sum('offer_price');

            $supplerCheck = DB::table('main_users')->where('id',$cartSupplierData->supplier_id)->first();
            // echo "<pre>";print_r($cartData);exit();
            $totalDicountPrice = @$cartTotalPrice - @$cartDiscountPrice;

            $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;

            $transaction_id = uniqid();
        $uniqid = uniqid();

        $order = new Order();
        $order->uniqid = $uniqid;
        $order->transaction_id = $transaction_id;
        $order->supplier_id = $cartSupplierData->supplier_id;
        $order->user_id = $user_id;
        $order->delivery_address_id = @$userAddress->id;
        $order->order_type = 2;
        $order->total_amount = $cartTotalPrice;
        $order->payable_amount = $totalCartAmount;
        $order->order_date = date('Y-m-d H:i:s');
        $order->order_time = date('Y-m-d H:i:s');
        $order->order_status = 0;
        $order->status = 1;

        $order->save();

        $number = str_pad($order->id,6,'0',STR_PAD_LEFT);
        $order_id = 'T25'.$number;

        $updatepsw = Order::where('user_id', $user_id)->where('id',$order->id)->update(array(
                   'order_id' => @$order_id,
                ));

        $tracking_id = uniqid();

        $order_tracking = new OrderTracking();
        $order_tracking->order_id = $order->id;
        $order_tracking->uniqid = $tracking_id;
        $order_tracking->order_status = 0;
        $order_tracking->status = 1;

        $order_tracking->save();

        foreach ($cartData as $cart) {
            $order_detail = new OrderDetails(); 
            $order_detail->order_id = @$order->id;   
            $order_detail->product_id = @$cart->product_id;   
            $order_detail->quantity = @$cart->quantity;   
            $order_detail->supplier_id = @$cart->supplier_id;   
            $order_detail->status = 1;

            $order_detail->save(); 
        }

        $trans_no = md5(rand(1, 10) . microtime());

        $transactions = new Transactions();
        $transactions->trans_no = $trans_no;
        $transactions->user_id = $user_id;
        $transactions->supplier_id = @$cartSupplierData->supplier_id;
        $transactions->order_id = @$order->id;
        $transactions->amount = $totalCartAmount;
        $transactions->transaction_date = date('Y-m-d H:i:s');
        $transactions->status = 1;

        $transactions->save();

        $user_payment = new UsersPayments();
        $user_payment->user_id = $user_id;
        $user_payment->supplier_id = @$cartSupplierData->supplier_id;
        $user_payment->order_id = @$order->id;
        $user_payment->transaction_id = @$transactions->id;
        $user_payment->payment_mode = 2;
        $user_payment->status = 1;

        $user_payment->save();

        $updatepsw = Cart::where('user_id', $user_id)->update(array(
                   'status' => 2,
                ));

        $userData = DB::table('main_users')->where('id',$user_id)->first();
        $title = "Pick-up Order Confirm";
        $message = "Pick-up Order Confirm";
        $remember_token = "cyHePWLCmELqvi4nUSw-qE:APA91bEKD5z6rCn4wXOcfSQsl5Il69DIsoEE3-OmfH2-lfe4OlsPPB17CT0jCOuU9QcKxNcDX8IhZnwihNVvucOy45bOpltsKA608_xaJdO24vmgF6oa2QB8qG-E14Ak_krtpkabPb0a";
        $device_token = $userData->device_token;
        // echo "<pre>";print_r($device_token);exit();
        $device_type = 1;

        $notification = new Notification();

        $notification->sender_id = @$user_id;
        $notification->receiver_id = @$user_id;
        $notification->notification_type = 1;
        $notification->title = @$title;
        $notification->message = @$message;
        $notification->is_read = 0;
        $notification->save();

         $response = (new \Helper)->send_notification_FCM($remember_token, $title, $message, $device_type);
        $response = (new \Helper)->sendNotification($device_token, $title, $message, $device_type);

        $store_name = DB::table('main_users')->where('id',$cartSupplierData->supplier_id)->first();

        $mainArr = [];
        $mainArr['order_id'] = $order_id;
        $mainArr['trans_no'] = $trans_no;
        $mainArr['store_name'] = $store_name->store_name;

                    $result['code']     =  strval(1);
                    $result['message']  =   'success';
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

    public function selectAddress(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $product_id = $request->product_id;
        $quantity = $request->quantity;

         $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid,$request->token);
         // echo "<pre>";print_r();exit();
         if ($response['code'] != 1) {
             $mainResult   =   $response;
             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
         }

         if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $user_id = $userData->id;
            // echo "<pre>";print_r($user_id);exit();
            $address_id = $request->address_id;

            $updatepsw = UserAddress::where('user_id', $user_id)->update(array(
                   'is_selected_address_id' => 0,
                ));

        $updateaddress = UserAddress::where('user_id', $user_id)->where('id',$address_id)->update(array(
                   'is_selected_address_id' => 1,
                ));

                $result['code']     =  strval(1);
                $result['message']  =   'success';
                // $result['result'][]   = [];
            
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

    public function getCartCount(Request $request){


        logger()->info("+++++++++++++++++++++++++++CartController - getCartCount+++++++++++++++++++++++");
        logger()->info($request->all());
        
        logger()->info("-----------------------------------------");
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
        if ($response['code'] != 1) {
            $mainResult   =   $response;
            return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if($post)
        {
            $userData = DB::table('main_users')->where('uniqid',$request->uniqid)->first();
            $count = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $userData->id)->where('cart.status', 1)->count();

            $result['code']     =  strval(1);
            $result['message']  =   'success';
            $result['cart_count']  = strval(@$count);

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

    public function storeListing(Request $request)
    {
        
                logger()->info("storeListing");

        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            // 'longitude'=>'required',
            // 'latitude'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error'=>$validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();
        if($post)
        {
            $longitude = $request->longitude;   
            $latitude = $request->latitude; 
            $keywords = $request->keywords;
            $language = $request->language;

            $mainArr = [];
            $result = [];
           
            $setting = Setting::find(1);
            $diff = $setting->map_distance;
           
            if( $longitude !="" && $latitude!=""){
              
                $storeMapData = DB::table('store_details')->select('store_details.*', DB::raw("(6371 * acos(cos(radians('" . $latitude . "')) * cos(radians(store_details.latitude)) * cos( radians(store_details.longitude) - radians('" . $longitude . "')) + sin(radians('" . $latitude . "')) * sin(radians(store_details.latitude)))) as distance"))->where('store_details.status', 1)
                ->havingRaw('distance <=' . $diff)
                ->orderBy('distance', 'ASC');
            }else{
                $storeMapData = DB::table('store_details')
                               // ->join('store_timing_week', 'store_timing_week.store_id', '=', 'store_details.id')
                                ->select('store_details.*')
                                ->orderby('store_details.id', 'DESC')
                                ->where('store_details.status', 1);
            }

            if ($keywords!="") {
                $storeMapData = $storeMapData->where('store_details.store_name','like','%'.$keywords.'%')->get();               
            }else{
                $storeMapData = $storeMapData->get();    
            }
          
            if (count($storeMapData) > 0) {
                $storeArr = [];
                $store_list = [];
                foreach ($storeMapData as $data) {     
                    $date = date('m/d/Y h:i:s a', time());
                    $weekname = date('l', strtotime($date));                                       
                    $weekhours = @Helper::weeklist($weekname);            
                    $storetime = @Helper::storeTime($data->id,$weekhours->id);
                    $start_time =date("H:i", strtotime($storetime->start_time));
                    $end_time = date("H:i", strtotime($storetime->end_time));
                    $store_time = NULL;
                    if($start_time!='00:00' || $end_time!='00:00'){
                        $store_time = $start_time.' to '.$end_time;
                    }
                    $distance = number_format(@$data->distance,2) ;
                    if($language==1){
                        $store_name  = ($data->store_name_fr)?$data->store_name_fr:$data->store_name;
                    }else{
                        $store_name  = $data->store_name;
                    }

                    $contactNumber = '';
                    if($data->phone_code){
                        $contactNumber .= '+'.$data->phone_code.' ';
                    }
                    if($data->contact_number){
                        $contactNumber .= $data->contact_number;
                    }

                    $storeArr['store_id'] = strval(@$data->id);
                    $storeArr['store_name'] = strval(@$store_name);
                    $storeArr['store_address'] = strval(@$data->address);
                    $storeArr['store_distance'] = strval(@$distance);
                    $storeArr['store_business_hours'] = strval(@$store_time);
                    $storeArr['store_contact_number'] = strval(@$contactNumber);
                   // $store['store_delivery_pickup'] = strval(@$data->store_name);
                   $store_list['store_list'][] = $storeArr;
                }
              

                $result['code']     =  strval(1);
                $result['message']  =  'success';
                $result['result']   = $store_list;
        
                $mainResult   =   $result;
                return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }else{
                $result['code']     =  strval(0);
                $result['message']  =  'no_data_found';
                $result['result']   =  NULL;
            
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

}
