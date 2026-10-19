@extends('frontend.layouts.app')
@section('title', 'Product List')
@section('content')
@include('sweetalert::alert')

<style>
      .bogo {
        /* position: absolute; */
        top: 5px;
        right: 14px;
        background-color: #fbb516;
        color: #242424;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        text-transform: uppercase;
        padding:4px 12px;
        z-index: 1;
        border-radius: 28px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        letter-spacing: 0.5px;
       transition: all 0.3s ease-in-out;
        transform: scale(1);
    }

     .bogo-cart {
        position: absolute;
        top: 0px;
        right: 0px;
        background-color: #fbb516;
        color: #242424;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.5;
        text-transform: uppercase;
        padding:8px 12px;
        z-index: 1;
        border-radius: 28px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        letter-spacing: 0.5px;
        transition: all 0.3s ease-in-out;
        transform: scale(1);
    }

    @media only screen and (max-width: 500px) {
        .bogo-cart{
                top: -7px !important;
                right: -10px !important;
                font-size: 7px !important;
                line-height: 0.5 !important;
                padding: 6px 7px !important;
        }

        .offer-cart
        {
                top: -7px !important;
                right: -10px !important;
                font-size: 7px !important;
                line-height: 0.5 !important;
                padding: 6px 7px !important;
        }
    }
    

    .bogo:hover {
        transform: scale(1.05);
    }

     .offer {
        /* position: absolute; */
        top: 5px;
        right: 14px;
        background-color: #fbb516;
        color: #242424;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        text-transform: uppercase;
        padding:4px 12px;
        z-index: 1;
        border-radius: 28px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        letter-spacing: 0.5px;
       transition: all 0.3s ease-in-out;
        transform: scale(1);
    }

    .offer-cart {
        position: absolute;
        top: 0px;
        right: -10px;
        background-color: #fbb516;
        color: #242424;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        text-transform: uppercase;
        padding:8px 12px;
        z-index: 1;
        border-radius: 28px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
        letter-spacing: 0.5px;
        transition: all 0.3s ease-in-out;
        transform: scale(1);
    }

    .offer:hover {
        transform: scale(1.05);
    }


    
      .swal-custom-confirm {
            background: #fbb516 !important; 
            color: black !important;
            border: 1px solid #fbb516 !important;
        }

        .swal-custom-confirm:focus {
            outline: none !important; 
            /* box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.5) !important;  */
            box-shadow:none !important; 
        }

        .swal2-icon.swal2-success {
            border-color: black !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border: 4px solid #fbb516 !important;
        }

        .swal2-icon.swal2-success .swal2-success-line-tip,
        .swal2-icon.swal2-success .swal2-success-line-long {
            background-color: #fbb516 !important;
        }


        .custom-swipper{
                height: 482px !important;
        }

        @media only screen and (max-width: 500px) {
            .custom-swipper{
                      height: 400px !important;
            }

            .bs-image {
                height: 300px;
            }

            .bs-image img {
                height: 280px;
            }
        }

        @media only screen and (min-width: 501px) and (max-width: 800px) {
                .custom-swipper {
                    height: 400px !important;
                }
        }
</style>

    <div class="bread-crumb-block">
        <div class="container">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">{{@Helper::language('home')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{@Helper::language('my_cart')}}</li>
            </ul>
        </div>
    </div>
    <!-- Cart -->
    <section class="cart pt-20">
        <div class="container">
            <div class="row">
                @if (!empty($productData) && count($productData) > 0)
                <div class="col-md-8">
                    <div class="cart-top">
                        <h2 class="mb-0">{{@Helper::language('my_cart')}}</h2>
                        <a href="{{ route('frontend.home') }}" class="link-button">{{@Helper::language('add_items')}}</a>
                    </div>
                    <div class="cart-item">
                        @if (!empty($productData) && count($productData) > 0)
                        @php
                            $original_price =0;
                            $total_discount_price =0;
                            $is_product_discount = false;
                            $total_quantity = 0;
                        @endphp
                            <ul class="cart-item-lists">
                                @foreach ($productData as $result)
                                    @php
                                        $product_title = '';
                                        if (session::get('language') == 1) {
                                            $product_title = $result->get_product_details->product_name;
                                        } else {
                                            $product_title = $result->get_product_details->product_name_fr ? $result->get_product_details->product_name_fr : $result->get_product_details->product_name;
                                        }
                                        $product_image = $result->get_product_details->get_product_images->first();
                                        
                                        $bogo_cart = $result->cart->where('user_id', $user_id)->sortByDesc('id')->first();
                                        $is_bogo = $bogo_cart ? $bogo_cart->is_bogo : 0;

                                        $is_offer = $bogo_cart ? $bogo_cart->is_offer : 0;
                                        $discount_amount = $bogo_cart ? $bogo_cart->discount_amount : null;
                                        $offer_type = $bogo_cart ? $bogo_cart->offer_type : null;

                                        $product_unit = Helper::getUnitById($result->variant_uof);
                                        if(Session::get('cart_info')!='' && $user_id=='' ){
                                            $is_bogo=Helper::getCartBogoStatus($result->id);
                                            $is_offer=Helper::getCartOfferStatus($result->id);
                                            $discount_amount=Helper::getCartDiscountAmount($result->id);
                                            $offer_type=Helper::getCartOfferType($result->id);

                                            // $org_price = @$result->variant_price ? ($result->variant_price * Helper::getCartQuantity($result->id)): 0;
                                            // $original_price += $org_price;
                                            // $discount_price = 0;
                                            // if($result->variant_discounted_price!='' && $result->variant_discounted_price!=0){
                                            //     $discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getCartQuantity($result->id)) : 0; 
                                            //     $is_product_discount =  true;
                                            // }else{
                                            //     $discount_price = $org_price;
                                            // }
                                            // $total_discount_price += ($org_price - $discount_price);

                                            $qty = Helper::getCartQuantity($result->id);
                                            $org_price = $result->variant_price ? ($result->variant_price * $qty) : 0;
                                            $original_price += $org_price;
                                            $discount_price = $org_price;

                                            if ($is_offer) {
                                                if ($offer_type == 'flat') {
                                                    // Multiply flat discount per item
                                                    $discount_price = max(0, $org_price - ($discount_amount * $qty));
                                                } elseif ($offer_type == 'percentage') {
                                                    $discount_price = max(0, $org_price - ($org_price * $discount_amount / 100));
                                                }
                                                $is_product_discount = true;
                                            } elseif ($result->variant_discounted_price && $result->variant_discounted_price != 0) {
                                                $discount_price = $result->variant_discounted_price * $qty;
                                                $is_product_discount = true;
                                            }

                                            $total_discount_price += ($org_price - $discount_price);

                                        }else{
                                            // $org_price = @$result->variant_price ? ($result->variant_price * Helper::getUserCartQuantity($result->id,$user_id)): 0;
                                            // $original_price += $org_price;
                                            // $discount_price = 0;

                                            // if($result->variant_discounted_price!='' && $result->variant_discounted_price!=0){
                                            //     $discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getUserCartQuantity($result->id,$user_id)) : 0; 
                                            //     $is_product_discount =  true;
                                            // }else{
                                            //     $discount_price = $org_price;
                                            
                                            // }
                                            // $total_discount_price += ($org_price - $discount_price);


                                            $qty = Helper::getUserCartQuantity($result->id, $user_id);
                                            $org_price = $result->variant_price ? ($result->variant_price * $qty) : 0;
                                            $original_price += $org_price;
                                            $discount_price = $org_price;

                                            if ($is_offer) {
                                                if ($offer_type == 'flat') {
                                                    // Multiply flat discount per item
                                                    $discount_price = max(0, $org_price - ($discount_amount * $qty));
                                                } elseif ($offer_type == 'percentage') {
                                                    $discount_price = max(0, $org_price - ($org_price * $discount_amount / 100));
                                                }
                                                $is_product_discount = true;
                                            } elseif ($result->variant_discounted_price && $result->variant_discounted_price != 0) {
                                                $discount_price = $result->variant_discounted_price * $qty;
                                                $is_product_discount = true;
                                            }

                                            $total_discount_price += ($org_price - $discount_price);

                                        }
                                             
                                    @endphp
                                    <li>
                                        <div class="single-product">
                                            <div class="product-img">
                                                <a
                                                    href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->get_product_details->id)]) }}">
                                                    @if (file_exists(public_path() . '/uploads/product/' . $product_image->image))
                                                        <img src="{{ asset('uploads/product/' . $product_image->image) }}"
                                                            title="{{ $product_title }}" alt="{{ $product_title }}" />
                                                    @else
                                                        <img src="{{ asset('assets/frontend/images/image-not-avilable.png') }}"
                                                            title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="product-detail">
                                                @if ($is_bogo)<h6 class="bogo-cart">{{@Helper::language('bogo')}}</h6>@endif

                                                @if($is_offer && !$is_bogo)
                                                    <h6 class="offer-cart">
                                                            @if ($offer_type === 'flat')
                                                                Flat {{ intval($discount_amount) }} {{ Helper::Settings('currency_symbol') }} Off
                                                            @elseif ($offer_type === 'percentage')
                                                                {{ intval($discount_amount) }}% Off
                                                            @endif
                                                    </h6>
                                                @endif

                                                <h6 class="mb-1 title-one"><a
                                                        href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->get_product_details->id)]) }}"
                                                        class="title-one mb-0">{{ @ucfirst($product_title) ?: '' }}</a></h6>
                                                <h6 class="quantity">{{@Helper::language('volume')}}
                                                    <span>:
                                                        {{ @$result->variant_size ? $result->variant_size . ' ' . $product_unit : '' }}</span>
                                                </h6>
                                                <ul>
                                                    {{-- <li class="product-pricing">
                                                        @if($result->variant_discounted_price==''  || $result->variant_discounted_price==0)
                                                        <h5>{{ @$result->variant_price ? $result->variant_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                        </h5>
                                                        @else
                                                        <h5>{{ @$result->variant_discounted_price ? $result->variant_discounted_price . Helper::Settings('currency_symbol') : '' }}<span>{{ @$result->variant_price ? $result->variant_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                        @endif
                                                    </li> --}}

                                                    <li class="product-pricing">
                                                        @php
                                                            $single_original_price = $result->variant_price;
                                                            $final_price = $single_original_price;

                                                            if ($is_offer) {
                                                                if ($offer_type === 'flat') {
                                                                    $final_price = max(0, $single_original_price - $discount_amount);
                                                                } elseif ($offer_type === 'percentage') {
                                                                    $final_price = max(0, $single_original_price - ($single_original_price * $discount_amount / 100));
                                                                }
                                                            }
                                                        @endphp

                                                        @if($final_price < $single_original_price)
                                                            <h5>
                                                                {{ $final_price }}{{ Helper::Settings('currency_symbol') }}
                                                                <span>{{ $single_original_price }}{{ Helper::Settings('currency_symbol') }}</span>
                                                            </h5>
                                                        @else
                                                            <h5>{{ $single_original_price }}{{ Helper::Settings('currency_symbol') }}</h5>
                                                        @endif
                                                                                                            
                                                    </li>

                                                    @php
                                                        $qty = ""; 
                                                        if (Session::get('cart_info') != '' && $user_id==""){
                                                            $qty = Helper::getCartQuantity($result->id);
                                                        }elseif ($user_id!="") {
                                                            $qty = Helper::getUserCartQuantity($result->id,$user_id);
                                                        } 

                                                    // Update total quantity logic
                                                        // $effective_qty = $is_bogo ? ($qty * 2) : $qty;
                                                        // $total_quantity += $effective_qty;
                                                        $total_quantity += $qty;
                                                        
                                                    @endphp
                                                    <li>

                                                        <span class="counter mb-0">
                                                                <input class="counter__input" type="text"
                                                                    value="{{$qty}}"
                                                                    name="counter" size="5" id="qty_ince_{{$result->id}}" readonly="readonly" />

                                                         <!-- Increment -->
                                                        <button type="button" class="counter__increment btn-increment" 
                                                            data-id="{{$result->id}}" 
                                                            data-product="{{ Helper::encodeUrl($result->get_product_details->id) }}"
                                                            data-offer_status="{{$is_offer}}"
                                                            style="background: none; border: none; padding: 0; cursor: pointer;">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g id="plus">
                                                                    <path id="Vector"
                                                                        d="M19.1999 11.2H12.8V4.79995C12.8 4.35845 12.4416 4 11.9999 4C11.5584 4 11.2 4.35845 11.2 4.79995V11.2H4.79995C4.35845 11.2 4 11.5584 4 11.9999C4 12.4416 4.35845 12.8 4.79995 12.8H11.2V19.1999C11.2 19.6416 11.5584 20 11.9999 20C12.4416 20 12.8 19.6416 12.8 19.1999V12.8H19.1999C19.6416 12.8 20 12.4416 20 11.9999C20 11.5584 19.6416 11.2 19.1999 11.2Z"
                                                                        fill="#242424" />
                                                                </g>
                                                            </svg>
                                                        </button>

                                                        <!-- Decrement -->
                                                        <button type="button" class="counter__decrement btn-decrement" 
                                                            data-id="{{$result->id}}" 
                                                            data-product="{{ Helper::encodeUrl($result->get_product_details->id) }}"
                                                            data-offer_status="{{$is_offer}}"
                                                            style="background: none; border: none; padding: 0; cursor: pointer;">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M4.95996 12.9203H19.0399C19.5702 12.9203 20 12.4905 20 11.9601C20 11.4298 19.5703 11 19.0399 11H4.95996C4.4298 11.0001 4 11.4299 4 11.9602C4 12.4905 4.4298 12.9203 4.95996 12.9203Z"
                                                                            fill="#242424" />
                                                            </svg>
                                                        </button>

                                                        </span>

                                                    </li>
                                                    <li class="border-top-0">
                                                        <a href="javascript:void(0)"
                                                            onclick="removeCartItem('{{ $result->id }}')"
                                                            class="link-button">{{@Helper::language('remove_btn')}}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <h3 class="text-center mb-30 text-danger">{{@Helper::language('no_data_found')}}</h3>
                        @endif
                    </div>
                </div>

                   <input type="hidden" value="{{ $total_quantity}}" name="basket_quantity" id="basket_quantity">
                <div class="col-md-4">
                    <div class="cart-price-block">
                        <table class="grey-card price-details">
                            <thead>
                                <tr>
                                    {{-- <th class="heading-six" colspan="2">{{@Helper::language('price_detail_label')}} ( {{@count($productData)?:'0'}} items)</th> --}}
                                    <th class="heading-six" colspan="2" id="basket_total"> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{@Helper::language('total_price')}}</td>
                                    <td><span id="product_total_price"> {{@Helper::numberFormat($original_price)}}</span> {{Helper::Settings('currency_symbol')}}</td>
                                    <input type="hidden" value="{{@Helper::numberFormat($original_price)}}" name="inp_product_total_price" >
                                
                                </tr>
                                @if( $is_product_discount == true)
                                <tr>
                                    <td> {{@Helper::language('discount_label')}}</td>
                                    <td>&#8722;<span id="product_discount_price">{{@Helper::numberFormat($total_discount_price)}} </span> {{Helper::Settings('currency_symbol')}}</td>
                                    <input type="hidden" value="{{$total_discount_price}}" name="inp_product_discount_price" >
                                </tr>
                                @endif
                                @php
                                $final_amount = @Helper::numberFormat(($original_price - $total_discount_price));                               
                                @endphp 
                                @if(Helper::Settings('tax')!=0)
                                <tr>
                                    <td>{{@Helper::language('tax')}} ({{Helper::Settings('tax')}}%)</td>
                                    <td>
                                        @php                                           
                                            $tax_amount = ((int) Helper::Settings('tax') / 100) * $final_amount;
                                        @endphp                                        
                                        <span id="tax_amount"> {{@Helper::numberFormat($tax_amount)}}</span> {{Helper::Settings('currency_symbol')}}
                                        <input type="hidden" value="{{$tax_amount}}" name="inp_tax_amount" >
                                    </td>
                                </tr>
                                @php
                                    $final_amount = @Helper::numberFormat($final_amount + $tax_amount);
                                @endphp
                                @endif
                                <tr>
                                    <td class="p-0" colspan="2">
                                        <hr class="m-0">
                                    </td>
                                </tr>
                                <tr class="total-amount">
                                    <td class="heading-five">{{@Helper::language('total_amount_label')}}</td>
                                    <td class="heading-five">
                                        <span id="total_amount">{{$final_amount}}</span>
                                        {{Helper::Settings('currency_symbol')}}
                                    </td>
                                    <input type="hidden" value="{{$final_amount}}" name="inp_total_amount" >
                                </tr>
                                <tr>
                                    <td colspan="2">
                                         <a href="{{route('checkout')}}"  class="solid-button w-100">{{@Helper::language('go_to_checkout')}}</a>    
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                {{-- <h3 class="text-center mb-30 text-danger">{{@Helper::language('no_data_found')}}</h3> --}}
                <div class="col-md-12" align="center">
                    <!-- <div class="cart-box clearfix"> -->
                    <img src="https://staghaute.vrinsoft.in/images/cartempty.png" style="width: 10%" data-pagespeed-url-hash="3916564767" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                    <h3><center>It feels so light !!</center></h3>
                    <h4><center>Your shopping cart is empty.Let's add some items.</center></h4>
                    <!-- </div> -->
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- Cart End -->
    <!-- Recently Viewed -->
    @if (!empty($recent_view_products) && count($recent_view_products) > 0)
    <section class="best-seller py-60">
        <div class="container">
            <h2 class="mb-30">{{@Helper::language('recently_viewed')}}</h2>
            <div class="best-seller-wrapper">
                <div class="swiper best-seller-slider recently-viewed-slider pb-30">
                    <div class="swiper-wrapper custom-swipper">
                        @if (!empty($recent_view_products) && count($recent_view_products) > 0)
                            @foreach ($recent_view_products as $key2=>$result)
                                @php
                                    $fav_data = Helper::userFavoriteProduct($result->id);

                                    $product_title = '';

                                    if (session::get('language') == 1) {
                                        $product_title = $result->product_name;
                                    } else {
                                        $product_title = $result->product_name_fr ? $result->product_name_fr : $result->product_name;                                     
                                    }
                                    $product_image = $result->get_product_images->first();
                                    $product_variant = $result->get_product_variants->first();
                                    $product_unit = Helper::getUnitById($product_variant->variant_uof);

                                    // Cart
                                    $gdisplay = 'none';
                                    $adisplay = 'flex';
                                    $is_in_cart = false;
                                    $user_id = auth()->guard('user')->id();
                                    $product_id = $result->id;
                                    $variant_id = $product_variant->id;
                                    $available_qty = $product_variant->available_qty ?? 0;

                                    $get_cart_array = Session::get('cart_info');

                                    if(isset($get_cart_array)){           
                                        if(array_key_exists($product_id,$get_cart_array)){
                                            foreach($get_cart_array as $key => $variant_array){   
                                                if($key==$product_id && array_key_exists($variant_id,$variant_array)){                       
                                                    $is_in_cart = true;
                                                }
                                            }
                                        }
                                    }else{                                    
                                        if($user_id) {
                                            $cartItem = DB::table('cart')
                                                ->where('user_id', $user_id)
                                                ->where('product_id', $product_id)
                                                ->where('status', 1)
                                                ->first();

                                            if($cartItem) {
                                                $is_in_cart = true;
                                            }
                                        }
                                    }   

                                    if($is_in_cart==true){                                   
                                        $gdisplay = 'flex';
                                        $adisplay = 'none';
                                    }
                                @endphp
                                <div class="swiper-slide">
                                    <div class="bs-box">
                                        <div class="bs-image">
                                            
                                              @if ($result->bogo_status)
                                                    <div class="mb-1 mt-1">
                                                        <span class="bogo" id="bogo">
                                                            {{@Helper::language('bogo')}}
                                                        </span>
                                                    </div>
                                                @endif

                                                @if ($result->offer_status && !$result->bogo_status)
                                                    <div class="mb-1 mt-1">
                                                        <span class="offer" id="offer">
                                                            @if ($result->offer_type === 'flat')
                                                                Flat {{ intval($result->discount_amount) }} {{ Helper::Settings('currency_symbol') }} Off
                                                            @elseif ($result->offer_type === 'percentage')
                                                                {{ intval($result->discount_amount) }}% Off
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif

                                            <a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->id)])}}" >
                                                @if (file_exists(public_path() . '/uploads/product/' . $product_image->image))
                                                    <img src="{{ asset('uploads/product/' . $product_image->image) }}"
                                                        title="{{ $product_title }}" alt="{{ $product_title }}" />
                                                @else
                                                    <img src="{{ asset('assets/frontend/images/image-not-avilable.png') }}"
                                                        title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="bs-content">
                                            <h6><a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}"
                                                    class="heading-six">{{ @ucfirst($product_title) ?: '' }}</a></h6>
                                            <span
                                                class="text-sm grey-text">{{ @$product_variant->variant_size ? $product_variant->variant_size . ' ' . $product_unit : '' }}</span>
                                            {{-- <div class="price-wrapper">
                                                @if($product_variant->variant_discounted_price=='' || $product_variant->variant_discounted_price==0)
                                                    <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                                @else
                                                    <span class="sell-price"> {{@$product_variant->variant_discounted_price?    $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                                    <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                                   
                                                @endif
                                            </div> --}}

                                            <div class="price-wrapper">
                                                @php
                                                    $original_price = $product_variant->variant_price;
                                                    $final_price = $original_price;

                                                    if ($result->offer_status && !$result->bogo_status) {
                                                        if ($result->offer_type === 'flat') {
                                                            $final_price = max(0, $original_price - $result->discount_amount);
                                                        } elseif ($result->offer_type === 'percentage') {
                                                            $final_price = max(0, $original_price - ($original_price * $result->discount_amount / 100));
                                                        }
                                                    }
                                                @endphp

                                                @if($final_price < $original_price)
                                                    <span class="sell-price">
                                                        {{ $final_price }}{{ Helper::Settings('currency_symbol') }}
                                                    </span>
                                                    <span class="original-price">
                                                        {{ $original_price }}{{ Helper::Settings('currency_symbol') }}
                                                    </span>
                                                @else
                                                    <span class="sell-price">
                                                        {{ $original_price }}{{ Helper::Settings('currency_symbol') }}
                                                    </span>
                                                @endif
                                            </div>


                                            @if (!empty($result->average_rating))
                                                <div class="product-rating">
                                                    <span
                                                        class="text-sm black-text">{{ @$result->average_rating ?: '' }}</span>
                                                    <i class="icon-star-fill"></i>
                                                </div>
                                            @endif
                                            {{-- <a title="{{Helper::language('add_to_cart')}}" href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}" class="add-bucket"><i class="icon-bucket"></i></a> --}}
                                           
                                            @if ($available_qty>0)
                                                <a style="display: <?php echo $adisplay ; ?>;" title="{{Helper::language('add_to_cart')}}"  data-product-id="{{ Helper::encodeUrl($product_id) }}"  data-variant-id="{{ $variant_id }}" data-bogo_status="{{ $result->bogo_status}}" data-offer_status="{{ $result->offer_status}}"  class="add-bucket"  href="javascript:void(0);"><i class="icon-bucket"></i></a>
                                            @endif

                                        </div>
                                        <input class="fav-icon checked_box" type="checkbox" id="fav-item{{$key2}}" value="{{ (isset($fav_data) && $fav_data != false) ? '1' : "0" }}" {{ (isset($fav_data) && $fav_data != false) ? 'checked' : "" }} onclick="return productFav({{$result->id}},{{ (isset($fav_data) && $fav_data != false) ? '1' : "0" }})" />
                                        <label title="{{Helper::language('add_to_favourite')}}" class="fav-button" for="fav-item{{$key2}}"></label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                @if (!empty($recent_view_products) && count($recent_view_products) > 4)
                <div class="nav-btn-wrapper">
                    <div class="recently-viewed-button-prev common-btn-prev"></div>
                    <div class="recently-viewed-button-next common-btn-next"></div>
                </div>
                <div class="swiper-scrollbar best-seller-scrollbar recently-viewed-scrollbar common-scroll"></div>
                @endif
            </div>
        </div>
    </section>
    @endif
    <!-- End Recently Viewed -->
    <!-- Top Selling Products -->
    @if (isset($top_products) && count($top_products) > 0)
    <section class="best-seller py-60">
        <div class="container">
            <h2 class="mb-30">{{@Helper::language('top_selling_products_label')}}</h2>

            <div class="best-seller-wrapper">
                @if (isset($top_products) && count($top_products) > 0)
                    <div class="swiper best-seller-slider top-selling-slider pb-30">
                        <div class="swiper-wrapper custom-swipper">
                            @foreach ($top_products as $key=>$result)
                                @php
                                //$favData = \DB::table('favorite_product')->where('user_id',$user_id)->where('product_id',$result->id)->where('status',1)->first();
                                $fav_data = Helper::userFavoriteProduct($result->id);

                                    $product_title = '';
                                    if (session::get('language') == 1) {
                                        $product_title = $result->product_name;
                                    } else {
                                        $product_title = $result->product_name_fr ? $result->product_name_fr : $result->product_name;
                                    }
                                    $product_image = $result->get_product_images->first();
                                    $product_variant = $result->get_product_variants->first();
                                    $product_unit = Helper::getUnitById($product_variant->variant_uof);

                                    
                                    // Cart
                                    $gdisplay = 'none';
                                    $adisplay = 'flex';
                                    $is_in_cart = false;
                                    $user_id = auth()->guard('user')->id();
                                    $product_id = $result->id;
                                    $variant_id = $product_variant->id;
                                    $available_qty = $product_variant->available_qty ?? 0;

                                    $get_cart_array = Session::get('cart_info');

                                    if(isset($get_cart_array)){           
                                        if(array_key_exists($product_id,$get_cart_array)){
                                            foreach($get_cart_array as $key => $variant_array){   
                                                if($key==$product_id && array_key_exists($variant_id,$variant_array)){                       
                                                    $is_in_cart = true;
                                                }
                                            }
                                        }
                                    }else{                                    
                                        if($user_id) {
                                            $cartItem = DB::table('cart')
                                                ->where('user_id', $user_id)
                                                ->where('product_id', $product_id)
                                                ->where('status', 1)
                                                ->first();

                                            if($cartItem) {
                                                $is_in_cart = true;
                                            }
                                        }
                                    }   

                                    if($is_in_cart==true){                                   
                                        $gdisplay = 'flex';
                                        $adisplay = 'none';
                                    }
                                @endphp
                                <div class="swiper-slide">
                                    <div class="bs-box">
                                        <div class="bs-image">

                                              @if ($result->bogo_status)
                                                    <div class="mb-1 mt-1">
                                                        <span class="bogo" id="bogo">
                                                            {{@Helper::language('bogo')}}
                                                        </span>
                                                    </div>
                                                @endif

                                                @if ($result->offer_status && !$result->bogo_status)
                                                    <div class="mb-1 mt-1">
                                                        <span class="offer" id="offer">
                                                            @if ($result->offer_type === 'flat')
                                                                Flat {{ intval($result->discount_amount) }} {{ Helper::Settings('currency_symbol') }} Off
                                                            @elseif ($result->offer_type === 'percentage')
                                                                {{ intval($result->discount_amount) }}% Off
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            
                                            <a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->id)])}}" >
                                                @if (file_exists(public_path() . '/uploads/product/' . $product_image->image))
                                                    <img src="{{ asset('uploads/product/' . $product_image->image) }}"
                                                        title="{{ $product_title }}" alt="{{ $product_title }}" />
                                                @else
                                                    <img src="{{ asset('assets/frontend/images/image-not-avilable.png') }}"
                                                        title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="bs-content">
                                            <h6><a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}"
                                                    class="heading-six">{{ @ucfirst($product_title) ?: '' }}</a></h6>
                                            <span
                                                class="text-sm grey-text">{{ @$product_variant->variant_size ? $product_variant->variant_size . ' ' . $product_unit : '' }}</span>
                                            {{-- <div class="price-wrapper">
                                                @if($product_variant->variant_discounted_price=="" || $product_variant->variant_discounted_price==0)
                                                    <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                                @else
                                                    <span class="sell-price"> {{@$product_variant->variant_discounted_price?    $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                                    <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                                    
                                                @endif
                                            </div> --}}

                                             <div class="price-wrapper">
                                                @php
                                                    $original_price = $product_variant->variant_price;
                                                    $final_price = $original_price;

                                                    if ($result->offer_status && !$result->bogo_status) {
                                                        if ($result->offer_type === 'flat') {
                                                            $final_price = max(0, $original_price - $result->discount_amount);
                                                        } elseif ($result->offer_type === 'percentage') {
                                                            $final_price = max(0, $original_price - ($original_price * $result->discount_amount / 100));
                                                        }
                                                    }
                                                @endphp

                                                @if($final_price < $original_price)
                                                    <span class="sell-price">
                                                        {{ $final_price }}{{ Helper::Settings('currency_symbol') }}
                                                    </span>
                                                    <span class="original-price">
                                                        {{ $original_price }}{{ Helper::Settings('currency_symbol') }}
                                                    </span>
                                                @else
                                                    <span class="sell-price">
                                                        {{ $original_price }}{{ Helper::Settings('currency_symbol') }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if (!empty($result->average_rating))
                                                <div class="product-rating">
                                                    <span
                                                        class="text-sm black-text">{{ @$result->average_rating ?: '' }}</span>
                                                    <i class="icon-star-fill"></i>
                                                </div>
                                            @endif
                                            {{-- <a title="{{Helper::language('add_to_cart')}}" href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}" class="add-bucket"><i class="icon-bucket"></i></a>                                            --}}

                                            @if ($available_qty>0)
                                                <a style="display: <?php echo $adisplay ; ?>;" title="{{Helper::language('add_to_cart')}}"  data-product-id="{{ Helper::encodeUrl($product_id) }}"  data-variant-id="{{ $variant_id }}" data-bogo_status="{{ $result->bogo_status}}" data-offer_status="{{ $result->offer_status}}" class="add-bucket"  href="javascript:void(0);"><i class="icon-bucket"></i></a>
                                            @endif
                                        </div>
                                        <input class="fav-icon checked_box" type="checkbox" id="fav-item1{{$key}}" value="{{ (isset($fav_data) && $fav_data != false) ? '1' : "0" }}" {{ (isset($fav_data) && $fav_data != false) ? 'checked' : "" }} onclick="return productFav({{$result->id}},{{ (isset($fav_data) && $fav_data != false) ? '1' : "0" }})" />
                                        <label class="fav-button" title="{{Helper::language('add_to_favourite')}}" for="fav-item1{{$key}}"></label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if (isset($top_products) && count($top_products) > 4)
                    <div class="nav-btn-wrapper">
                        <div class="top-selling-button-prev common-btn-prev"></div>
                        <div class="top-selling-button-next common-btn-next"></div>
                    </div>
                    <div class="swiper-scrollbar best-seller-scrollbar top-selling-scrollbar common-scroll"></div>
                    @endif
                @endif
            </div>
        </div>
    </section>
    @endif
    <!-- End Top Selling Products -->

@endsection
@push('after-scripts')

<script>
    const clickLock = {};

    $(document).on('click', '.btn-increment, .btn-decrement', function (event) {
        event.preventDefault();
        event.stopPropagation();

        const $button = $(this);
        const variantId = $button.data('id');
        const offer_status = $button.data('offer_status');

        const type = $button.hasClass('btn-increment') ? 'incr' : 'desc';
        const productId = $button.data('product'); // Add this as a data attribute in HTML

        if (clickLock[variantId]) return;
        clickLock[variantId] = true;

        let tquty = parseInt($("#qty_ince_" + variantId).val()) || 1;
        let quantity = tquty;

        if (type === 'incr') {
            quantity += 1;
        } else if (type === 'desc') {
            if (tquty === 1) {
                clickLock[variantId] = false;
                return;
            }
            quantity -= 1;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('cartincrement') }}",
            data: {
                product_id: productId,
                quantity: quantity,
                variantId: variantId,
                offer_status:offer_status
            },
            success: function(response) {
                $("#qty_ince_" + variantId).val(quantity);
                $("#product_total_price").text(response.total_products_price);
                $("#product_discount_price").text(response.total_discount_price);
                $("#tax_amount").text(response.tax_amount);
                $("#total_amount").text(response.total_amount);

                $("input[name='inp_product_total_price']").val(response.total_products_price);
                $("input[name='inp_product_discount_price']").val(response.total_discount_price);
                $("input[name='inp_tax_amount']").val(response.tax_amount);
                $("input[name='inp_total_amount']").val(response.total_amount);

                location.reload();
            },
            complete: function() {
                clickLock[variantId] = false;
            }
        });
    });



</script>

<script>
        function removeCartItem(variant_id) {
            Swal.fire({
                text: "{{@Helper::language('remove_cart_product')}}",
                showCancelButton: true,
                confirmButtonColor: "#fbb516",
                cancelButtonColor: "rgb(36, 36, 36)",
                confirmButtonText:  "{{@Helper::language('cart_yes')}}",
                cancelButtonText: "{{@Helper::language('cart_no')}}",
                customClass: {
                    confirmButton: 'swal-custom-confirm',
                    cancelButton: 'swal-custom-cancel',
                    },
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        data: {
                            variant_id: variant_id
                        },
                        url: "{{ route('cartremove') }}",
                        success: function(data) {
                            location.reload();
                        }
                    });
                }
            });

        }

  

    function productFav(product_id, status) {
        var status = status;
        var user_id = $("#user_id").val();
        if (user_id ) {
            action_url = "{{ route('productfav') }}";
        } else {
            var url = "{{route('websitelogin')}}";
            window.location.href = url;

        }
        var csrf = "{{ csrf_token() }}";
        $.ajax({
            url: action_url,
            data: {
                'user_id': user_id,
                'product_id': product_id,
                'status': status
            },
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            type: "POST",

            beforeSend: function() {
                $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function(response) {
                // return false;
                 if(user_id != ''){
                    location.reload();
                }else{
                    location.href="{{route('websitelogin')}}";
                }
            },
        });

    }
</script>

<script>
    // add to cart
    $(document).on('click', '.add-bucket', function(e) {
            e.preventDefault();

            var $this = $(this);
            $this.hide();

            var product_id = $this.data('product-id');
            var variantId = $this.data('variant-id');
            var bogo_status = $this.data('bogo_status');
            var offer_status = $this.data('offer_status');
            var quantity = 1;
            var action_url = "{{ route('productcartadd') }}";
            var csrf = "{{ csrf_token() }}";
            var currentCount = $(".cart-item-total-count").html();

            var added_product_message = "{{\Helper::language('product_added_to_cart_successfully')}}";
            var success_message = "{{\Helper::language('success')}}";

            $.ajax({
                url: action_url,
                data: {
                    'product_id': product_id,
                    'quantity': quantity,
                    'variantId': variantId,
                    'bogo_status':bogo_status,
                    'offer_status':offer_status
                },
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                type: "POST",
                beforeSend: function() {
                     $(".loader").fadeIn().css("visibility", "visible");
                },
                success: function(response) {
                    $('.loader').css("visibility", "hidden");
                    $('#cart-url').removeAttr("onclick").attr('href', '{{ route('cart') }}');
                    $(".cart-item-total-count").html(response.cart_count);
                    if (response.success == "true") {
                        updateCartUI();
                        // Swal.fire({
                        //     icon: "success",
                        //     title: success_message,
                        //     text: added_product_message,
                        //     customClass: {
                        //         confirmButton: 'swal-custom-confirm'
                        //     }
                        // }).then(() => {
                        //     location.reload();
                        // });;
                        
                        if (typeof shakeFloatingCart === 'function') shakeFloatingCart();
                    // Add shake animation to button
                    $this.addClass('shake');
                    $this.one('animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd', function() {
                        $this.removeClass('shake');
                    });
                        location.reload();
                    }
                },
            });
        });
</script>

{{-- Basket Total --}}
<script>
    let basket_quantity=document.getElementById('basket_quantity').value;
    let basket_total=document.getElementById('basket_total');

    basket_total.innerHTML=`Basket Total ( ${basket_quantity} items)`
</script>

@endpush
