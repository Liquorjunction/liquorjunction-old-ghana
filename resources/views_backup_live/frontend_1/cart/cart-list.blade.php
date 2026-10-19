@extends('frontend.layouts.app')
@section('title', 'Product List')
@section('content')
@include('sweetalert::alert')

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
                                        $product_unit = Helper::getUnitById($result->variant_uof);
                                        if(Session::get('cart_info')!='' && $user_id=='' ){
                                            $org_price = @$result->variant_price ? ($result->variant_price * Helper::getCartQuantity($result->id)): 0;
                                            $original_price += $org_price;
                                            $discount_price = 0;
                                            if($result->variant_discounted_price!='' && $result->variant_discounted_price!=0){
                                                $discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getCartQuantity($result->id)) : 0; 
                                                $is_product_discount =  true;
                                            }else{
                                                $discount_price = $org_price;
                                            }
                                            $total_discount_price += ($org_price - $discount_price);
                                        }else{
                                            $org_price = @$result->variant_price ? ($result->variant_price * Helper::getUserCartQuantity($result->id,$user_id)): 0;
                                            $original_price += $org_price;
                                            $discount_price = 0;

                                            if($result->variant_discounted_price!='' && $result->variant_discounted_price!=0){
                                                $discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getUserCartQuantity($result->id,$user_id)) : 0; 
                                                $is_product_discount =  true;
                                            }else{
                                                $discount_price = $org_price;
                                            
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
                                                <h6 class="mb-1 title-one"><a
                                                        href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->get_product_details->id)]) }}"
                                                        class="title-one mb-0">{{ @ucfirst($product_title) ?: '' }}</a></h6>
                                                <h6 class="quantity">{{@Helper::language('volume')}}
                                                    <span>:
                                                        {{ @$result->variant_size ? $result->variant_size . ' ' . $product_unit : '' }}</span>
                                                </h6>
                                                <ul>
                                                    <li class="product-pricing">
                                                        @if($result->variant_discounted_price==''  || $result->variant_discounted_price==0)
                                                        <h5>{{ @$result->variant_price ? $result->variant_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                        </h5>
                                                        @else
                                                        <h5>{{ @$result->variant_discounted_price ? $result->variant_discounted_price . Helper::Settings('currency_symbol') : '' }}<span>{{ @$result->variant_price ? $result->variant_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                        @endif
                                                    </li>
                                                    @php
                                                        $qty = ""; 
                                                        if (Session::get('cart_info') != '' && $user_id==""){
                                                            $qty = Helper::getCartQuantity($result->id);
                                                        }elseif ($user_id!="") {
                                                            $qty = Helper::getUserCartQuantity($result->id,$user_id);
                                                        } 
                                                    @endphp
                                                    <li>
                                                        <span class="counter mb-0">
                                                            <input class="counter__input" type="text"
                                                                value="{{$qty}}"
                                                                name="counter" size="5" id="qty_ince_{{$result->id}}" readonly="readonly" />
                                                            <a class="counter__increment" onclick="quntityIncreaseOrDecrease('{{Helper::encodeUrl($result->get_product_details->id)}}','{{$result->id}}','incr');"  href="javascript:void(0)" data-id="{{$result->id}}">
                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <g id="plus">
                                                                        <path id="Vector"
                                                                            d="M19.1999 11.2H12.8V4.79995C12.8 4.35845 12.4416 4 11.9999 4C11.5584 4 11.2 4.35845 11.2 4.79995V11.2H4.79995C4.35845 11.2 4 11.5584 4 11.9999C4 12.4416 4.35845 12.8 4.79995 12.8H11.2V19.1999C11.2 19.6416 11.5584 20 11.9999 20C12.4416 20 12.8 19.6416 12.8 19.1999V12.8H19.1999C19.6416 12.8 20 12.4416 20 11.9999C20 11.5584 19.6416 11.2 19.1999 11.2Z"
                                                                            fill="#242424" />
                                                                    </g>
                                                                </svg>
                                                            </a>
                                                            <a class="counter__decrement" style="cursor: pointer;"  onclick="quntityIncreaseOrDecrease('{{Helper::encodeUrl($result->get_product_details->id)}}','{{$result->id}}','desc');">
                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M4.95996 12.9203H19.0399C19.5702 12.9203 20 12.4905 20 11.9601C20 11.4298 19.5703 11 19.0399 11H4.95996C4.4298 11.0001 4 11.4299 4 11.9602C4 12.4905 4.4298 12.9203 4.95996 12.9203Z"
                                                                        fill="#242424" />
                                                                </svg>
                                                            </a>
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
                <div class="col-md-4">
                    <div class="cart-price-block">
                        <table class="grey-card price-details">
                            <thead>
                                <tr>
                                    <th class="heading-six" colspan="2">{{@Helper::language('price_detail_label')}} ( {{@count($productData)?:'0'}} items)</th>
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
                                        <a @if(auth()->guard('user')->user()=='') href="{{route('websitelogin')}}" @else href="{{route('checkout')}}" @endif  class="solid-button w-100">{{@Helper::language('go_to_checkout')}}</a>    
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
                    <div class="swiper-wrapper">
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
                                @endphp
                                <div class="swiper-slide">
                                    <div class="bs-box">
                                        <div class="bs-image">
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
                                            <div class="price-wrapper">
                                                @if($product_variant->variant_discounted_price=='' || $product_variant->variant_discounted_price==0)
                                                    <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                                @else
                                                    <span class="sell-price"> {{@$product_variant->variant_discounted_price?    $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                                    <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                                   
                                                @endif
                                            </div>
                                            @if (!empty($result->average_rating))
                                                <div class="product-rating">
                                                    <span
                                                        class="text-sm black-text">{{ @$result->average_rating ?: '' }}</span>
                                                    <i class="icon-star-fill"></i>
                                                </div>
                                            @endif
                                            <a title="{{Helper::language('add_to_cart')}}" href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}" class="add-bucket"><i class="icon-bucket"></i></a>
                                            <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
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
                        <div class="swiper-wrapper">
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
                                @endphp
                                <div class="swiper-slide">
                                    <div class="bs-box">
                                        <div class="bs-image">
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
                                            <div class="price-wrapper">
                                                @if($product_variant->variant_discounted_price=="" || $product_variant->variant_discounted_price==0)
                                                    <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                                @else
                                                    <span class="sell-price"> {{@$product_variant->variant_discounted_price?    $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                                    <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                                    
                                                @endif
                                            </div>
                                            @if (!empty($result->average_rating))
                                                <div class="product-rating">
                                                    <span
                                                        class="text-sm black-text">{{ @$result->average_rating ?: '' }}</span>
                                                    <i class="icon-star-fill"></i>
                                                </div>
                                            @endif
                                            <a title="{{Helper::language('add_to_cart')}}" href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}" class="add-bucket"><i class="icon-bucket"></i></a>                                           
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
        function removeCartItem(variant_id) {
            Swal.fire({
                text: "{{@Helper::language('remove_cart_product')}}",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText:  "{{@Helper::language('cart_yes')}}",
                cancelButtonText: "{{@Helper::language('cart_no')}}",
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

        // $(document).ready(function(){
        //     $(".counter__increment").on('click', function(e){
               
        //         alert();
        //     });
        //     $(".counter__decrement").on('click', function(e){
               
        //        alert();
        //    });
        // });

    function quntityIncreaseOrDecrease(product_id,variantId,type){
        var tquty = parseInt($("#qty_ince_"+variantId).val());
        var one = parseInt(1);
        if(type=='incr'){
            var quantity = (tquty+one);
        }else if(type=='desc'){
           if(tquty==1){
            return false;
           } var quantity = (tquty-one);           
        }
        $("#qty_ince_"+variantId).val(quantity);
        
        $.ajax({
            type: "post",
            data: {product_id:product_id,quantity:quantity,variantId: variantId
            },
            url: "{{ route('cartincrement') }}",
            success: function(response) {
                $("#product_total_price").text(response.total_products_price);
                $("#product_discount_price").text(response.total_discount_price);
                $("#tax_amount").text(response.tax_amount);
                $("#total_amount").text(response.total_amount);
                $("input[name='inp_product_total_price']").val(response.total_products_price);
                $("input[name='inp_product_discount_price']").val(response.total_discount_price);
                $("input[name='inp_tax_amount']").val(response.tax_amount);
                $("input[name='inp_total_amount']").val(response.total_amount);
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
@endpush
