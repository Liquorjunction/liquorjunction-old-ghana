@extends('frontend.layouts.app')
@section('title','Home')
@section('content')
@include('sweetalert::alert')

<style>
        /* @media only screen and (max-width: 767px) {
            .custom-highlight-text {
                color: #000000 !important; 
            }
        } */

        @media only screen and (max-width: 767px) {
            .custom-sizing {
                padding: 0px 10px;
            }
        }

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

    /* .bogo:hover {
        transform: scale(1.05);
    } */

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

    /* .offer:hover {
        transform: scale(1.05);
    } */

    .swal-custom-confirm {
        background: #fbb516 !important; 
        color: black !important;
        border: 1px solid #fbb516 !important;
    }

    .swal-custom-confirm:focus {
        outline: none !important; 
        /* box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.5) !important;  */
        box-shadow: none !important; 
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

    @media only screen and (max-width: 575px) {
        #custom-spacing{
            padding-top: 50px !important;
        }
    }
</style>



@php
if(session::get('language')==2){
    $no_data_found = "Aucune donnée disponible";
}else{
    $no_data_found = "No data found";
}
$user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';

@endphp
<div class="loader" id="loader"></div> 
<!-- Banner -->
<section class="banner">
    @if(isset($banners) && count($banners) > 0)
    <div class="swiper banner-slider">
        <div class="swiper-wrapper">
            @foreach ($banners as $banner_result )
            @php
            $banner_title='';
            $banner_description='';
            $image_not_found = '';
            
            if(session::get('language')==2){                
                $banner_title = ($banner_result->title_fr)?$banner_result->title_fr:$banner_result->title;
                $banner_description= ($banner_result->description_fr)?$banner_result->description_fr:$banner_result->description;         
            }else{               
                $banner_title = $banner_result->title;
                $banner_description= $banner_result->description;;  
            }
            @endphp
            <div class="swiper-slide">
                <div class="banner-wrapper">
                    <div class="banner-image">
                        @if (file_exists(public_path() . '/uploads/banners/'.$banner_result->photo))
                        <img src="{{ asset('uploads/banners/'.$banner_result->photo) }}" title="{{$banner_title}}" alt="{{$banner_title}}" />
                        @else
                        <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                        @endif
                    </div>
                    <div class="banner-content">
                        <div class="container">
                            <div class="row">
                            <div class="col-lg-11 offset-lg-1">
                        <div class="banner-text" style="color: {{ $banner_result->text_color }};">
                            <h1 style="color: {{ $banner_result->text_color }};">{{ @$banner_title }}</h1>
                            <p style="color: {{ $banner_result->text_color }};">{{ @$banner_description }}</p>
                                        <!-- <h1>{{@$banner_title}}</h1>
                                        <p>{{@$banner_description}}</p> -->
                                       
                                        @if($banner_result->type ==1 && $banner_result->category_id!="" && $banner_result->subcategory_id!="" )
                                            <a class="solid-button" href="{{route('productlist',['id'=>Helper::encodeUrl($banner_result->category_id)]);}}?sid={{Helper::encodeUrl($banner_result->subcategory_id)}}">{{@Helper::language('explore_more')}}</a>
                                        @elseif($banner_result->type ==1 && $banner_result->category_id!="" )
                                            <a href="{{route('productlist',['id' => Helper::encodeUrl($banner_result->category_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
                                        @elseif($banner_result->type ==2 &&$banner_result->product_id!="")
                                            <a href="{{route('productdetails',['id' => Helper::encodeUrl($banner_result->product_id)])}}" class="solid-button">{{@Helper::language('shop_now')}}</a>
                                        @elseif($banner_result->type ==0 && $banner_result->brand_id!="")
                                        <a href="{{route('filterbrandlist',['id' => Helper::encodeUrl($banner_result->brand_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
                                        @else
                                            @if(!empty($banner_result->banner_url))
                                                <a href="{{$banner_result->banner_url}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if( count($banners) > 1)
        <div class="banner-button-row">
            <div class="banner-button-prev common-btn-prev"></div>
            <div class="banner-button-next common-btn-next"></div>
        </div>
        @endif
    </div>
    @endif
</section>
<!-- End Banner -->

<!-- Shop By Spirit -->
<section class="shop-by-spirit py-60" id="custom-spacing">
    <div class="container">
        <h2 class="text-center mb-30">{{@Helper::language('shop_by_spirit')}}</h2>

        <div class="shop-slider-wrapper">
            @if(isset($categories) && count($categories) > 0)
            <div class="swiper shop-spirit-slider">
                <div class="swiper-wrapper">
                    @foreach ( $categories as $categories_result)
                    @php
                    if(session::get('language')==2){                    
                        $category_title = ($categories_result->title_fr)?$categories_result->title_fr:$categories_result->title;                     
                    }else{
                        $category_title = $categories_result->title;               
                    }
                    @endphp
                    <div class="swiper-slide">
                        <a href="{{route('productlist',['id'=>Helper::encodeUrl($categories_result->id)]);}}" class="product-wrapper">
                            <div class="product-image">
                                @if (file_exists(public_path() . '/uploads/category/'.$categories_result->imagefile))
                                <img src="{{ asset('uploads/category/'.$categories_result->imagefile) }}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}" />
                                @else
                                <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                                @endif
                            </div>
                            <h5>{{$category_title}}</h5>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @if(count($categories) > 10)
                <div class="nav-btn-wrapper">
                    <div class="shop-spirit-button-prev common-btn-prev"></div>
                    <div class="shop-spirit-button-next common-btn-next"></div>
                </div>
            @endif
            <div class="swiper-scrollbar shop-spirit-scrollbar common-scroll"></div>
            @else
            <h3 class="text-center mb-30 text-danger">{{@$no_data_found?:''}}</h3>
            @endif
        </div>
    </div>
</section>
<!-- End Shop By Spirit -->

<!-- Free Shipping -->
@if(!empty($banners_offer))
@php
if(session::get('language')==2){
    $boffer_title = ($banners_offer->title_fr)?$banners_offer->title_fr:$banners_offer->title;
    $boffer_description= ($banners_offer->description_fr)?$banners_offer->description_fr:$banners_offer->description;
}else{
    $boffer_title = $banners_offer->title;
    $boffer_description= $banners_offer->description;
}
@endphp
<section class="free-shipping" style="background-image: url('{{ asset('uploads/banners/'.$banners_offer->photo) }}');">
    <!-- <div class="container">
        <h2 class="m-0">{{$boffer_title}}</h2>
        <span>{{$boffer_description}}</span> -->
        <div class="container" style="color: {{ $banners_offer->text_color }};">
        <h2 class="m-0" style="color: {{ $banners_offer->text_color }};">{{ $boffer_title }}</h2>
        <span style="color: {{ $banners_offer->text_color }};">{{ $boffer_description }}</span>
        @if($banners_offer->type ==1 && $banners_offer->category_id!="" && $banners_offer->subcategory_id!="" )
            <a class="solid-button" href="{{route('productlist',['id'=>Helper::encodeUrl($banners_offer->category_id)]);}}?sid={{Helper::encodeUrl($banners_offer->subcategory_id)}}">{{@Helper::language('explore_more')}}</a>
        @elseif($banners_offer->type ==1 && $banners_offer->category_id!="" )
        <a href="{{route('productlist',['id' => Helper::encodeUrl($banners_offer->category_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
        @elseif($banners_offer->type ==2 &&$banners_offer->product_id!="")
        <a href="{{route('productdetails',['id' => Helper::encodeUrl($banners_offer->product_id)])}}" class="solid-button">{{@Helper::language('order_now')}}</a>
        @elseif($banners_offer->type ==0 && $banners_offer->brand_id!="")
        <a href="{{route('filterbrandlist',['id' => Helper::encodeUrl($banners_offer->brand_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
        @else
            @if(!empty($banners_offer->banner_url))
                <a href="{{$banners_offer->banner_url}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
            @endif
        @endif
    </div>
</section>
@endif
<!-- End Free Shipping -->


<!-- Offers -->
@if(isset($offer_product) && count($offer_product) > 0)
<section class="offers py-60">
    <div class="container">
        <h2 class="text-center mb-30 text-white">{{@Helper::language('offers')}}</h2>
        <div class="offers-slider-wrapper">
            @if(count($offer_product) > 0)
            <div class="swiper offers-slider pb-30">
                <div class="swiper-wrapper">
                    @foreach ($offer_product as $key1=>$offer_result )
                    @php
                    $product_title='';
                    if(session::get('language')==2){
                        $product_title = ($offer_result->product_name_fr)?$offer_result->product_name_fr:$offer_result->product_name;
                    }else{
                        $product_title = $offer_result->product_name;
                    }
                    $product_image = $offer_result->get_product_images->first();
                    $product_variant = $offer_result->get_product_variants->first();
                    $product_unit = Helper::getUnitById($product_variant->variant_uof);
                    $fav_data = Helper::userFavoriteProduct($offer_result->id);


                    // Cart
                    $gdisplay = 'none';
                    $adisplay = 'flex';
                    $is_in_cart = false;
                    $user_id = auth()->guard('user')->id();
                    $product_id = $offer_result->id;
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
                            <div class="bs-image responsive-image">

                                @if ($offer_result->bogo_status)
                                    <div class="mb-1 mt-1">
                                        <span class="bogo" id="bogo">
                                            {{@Helper::language('bogo')}}
                                        </span>
                                    </div>
                                @endif

                                @if ($offer_result->offer_status && !$offer_result->bogo_status)
                                    <div class="mb-1 mt-1">
                                        <span class="offer" id="offer">
                                            @if ($offer_result->offer_type === 'flat')
                                                Flat {{ intval($offer_result->discount_amount) }} {{ Helper::Settings('currency_symbol') }} Off
                                            @elseif ($offer_result->offer_type === 'percentage')
                                                {{ intval($offer_result->discount_amount) }}% Off
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                <a href="{{route('productdetails',['id'=>Helper::encodeUrl($offer_result->id)])}}" >
                                    @if (file_exists(public_path() . '/uploads/product/'.$product_image->image))					
                                    <img src="{{ asset('uploads/product/'.$product_image->image) }}" title="{{$product_title}}" />
                                    @else
                                    <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                                    @endif 
                                </a>
                            </div>
                            <div class="offers-content">
                                <h6>
                                    <a href="{{route('productdetails',['id'=>Helper::encodeUrl($offer_result->id)])}}" class="heading-six">{{@ucfirst($product_title)?: ''}} </a>
                                </h6>
                                <span class="text-sm grey-text">{{@$product_variant->variant_size? $product_variant->variant_size.' '.$product_unit :''}}</span>
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

                                        if ($offer_result->offer_status && !$offer_result->bogo_status) {
                                            if ($offer_result->offer_type === 'flat') {
                                                $final_price = max(0, $original_price - $offer_result->discount_amount);
                                            } elseif ($offer_result->offer_type === 'percentage') {
                                                $final_price = max(0, $original_price - ($original_price * $offer_result->discount_amount / 100));
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

                                @if(!empty($offer_result->average_rating))
                                <div class="product-rating">
                                    <span class="text-sm black-text">{{@$offer_result->average_rating?: ''}}</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                @endif
                                {{-- <a title="{{Helper::language('add_to_cart')}}" href="{{ route('productdetails', ['id' => Helper::encodeUrl($offer_result->id)]) }}" class="add-bucket"><i class="icon-bucket"></i></a> --}}

                                @if ($available_qty>0)
                                    <a style="display: <?php echo $adisplay ; ?>;" title="{{Helper::language('add_to_cart')}}"  data-product-id="{{ Helper::encodeUrl($product_id) }}"  data-variant-id="{{ $variant_id }}" data-bogo_status="{{ $offer_result->bogo_status}}" data-offer_status="{{ $offer_result->offer_status}}"  class="add-bucket"  href="javascript:void(0);"><i class="icon-bucket"></i></a>
                                @endif
                            </div>
                            <input class="fav-icon checked_box" type="checkbox" id="fav-item1{{$key1}}" value="{{ (isset($fav_data) && $fav_data != false) ? '1' : "0" }}" {{ (isset($fav_data) && $fav_data != false) ? 'checked' : "" }} onclick="return productFavTest({{$offer_result->id}},{{ (isset($fav_data) && $fav_data != false) ? '1' : "0" }})" />
                                <label class="fav-button" title="{{Helper::language('add_to_favourite')}}" for="fav-item1{{$key1}}"></label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="nav-btn-wrapper">
                <div class="offers-button-prev common-btn-prev"></div>
                <div class="offers-button-next common-btn-next"></div>
            </div>
            <div class="swiper-scrollbar offers-scrollbar common-scroll"></div>
            @else
            <h3 class="text-center mb-30 text-danger">{{@$no_data_found?:''}}</h3>
            @endif
        </div>
    </div>
</section>
@endif
<!-- End Offers -->

<!-- Our Highlights -->
@if(isset($banners_highlight) && count($banners_highlight) > 0)
<section class="our-highlights py-60">
    <div class="container">
        <h2 class="text-center mb-30">{{@Helper::language('our_highlights')}}</h2>
        @if(isset($banners_highlight) && count($banners_highlight) > 0)
        <div class="our-highlights-slider-wrapper">
            <div class="swiper our-highlights-slider pb-30">
                <div class="swiper-wrapper">
                    @foreach ($banners_highlight as $highlight_result )
                    @php
                    $banner_title='';
                    $banner_description='';
                    $image_not_found = '';
                    if(session::get('language')==1){
                    $banner_title = $highlight_result->title;
                    $banner_description= $highlight_result->description;
                    }else{
                    $banner_title = ($highlight_result->title_fr)?$highlight_result->title_fr:$highlight_result->title;
                    $banner_description= ($highlight_result->description_fr)?$highlight_result->description_fr:$highlight_result->description;
                    }
                    @endphp
                    <div class="swiper-slide custom-sizing">
                        <div class="our-highlights-box">
                            <div class="our-highlights-image">
                                @if (file_exists(public_path() . '/uploads/banners/'.$highlight_result->photo))
                                <img src="{{ asset('uploads/banners/'.$highlight_result->photo) }}" title="{{$banner_title}}" alt="{{$banner_title}}" />
                                @else
                                <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                                @endif
                            </div>
                            <div class="our-highlights-content">
                                <!-- <span>{{$banner_title}}</span>
                                <h3>{{$banner_description}}</h3> -->
                                <span class="custom-highlight-text" style="color: {{ $highlight_result->text_color }};">{{ $banner_title }}</span>
                                <h3 class="custom-highlight-text" style="color: {{ $highlight_result->text_color }};">{{ $banner_description }}</h3> 
                                {{--<a href="{{$highlight_result->banner_url}}" class="solid-button">{{@Helper::language('order_now')}}</a>--}}
                                <?php
                                //  dd($highlight_result->category_id );
                                ?>
                                @if($highlight_result->type ==1 && $highlight_result->category_id!="" && $highlight_result->subcategory_id!="" )
                                <a class="solid-button" href="{{route('productlist',['id'=>Helper::encodeUrl($highlight_result->category_id)]);}}?sid={{Helper::encodeUrl($highlight_result->subcategory_id)}}">{{@Helper::language('explore_more')}}</a>
                                @elseif($highlight_result->type ==1 && $highlight_result->category_id!="" )
                                <a href="{{route('productlist',['id' => Helper::encodeUrl($highlight_result->category_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
                                @elseif($highlight_result->type ==2 &&$highlight_result->product_id!="")
                                <a href="{{route('productdetails',['id' => Helper::encodeUrl($highlight_result->product_id)])}}" class="solid-button">{{@Helper::language('order_now')}}</a>
                                @elseif($highlight_result->type ==0 && $highlight_result->brand_id!="")
                                <a href="{{route('filterbrandlist',['id' => Helper::encodeUrl($highlight_result->brand_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
                                @else
                                <a href="{{$highlight_result->banner_url}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="nav-btn-wrapper">
                <div class="our-highlights-button-prev common-btn-prev"></div>
                <div class="our-highlights-button-next common-btn-next"></div>
            </div>
            <div class="swiper-scrollbar highlights-scrollbar common-scroll"></div>
        </div>
        @else
        <h3 class="text-center mb-30 text-danger">{{@$no_data_found?:''}}</h3>
        @endif
    </div>
</section>
@endif
<!-- End Our Highlights -->
<!-- Best Seller -->
@if(isset($best_seller_product) && count($best_seller_product) > 0)
<section class="best-seller py-60 offers">
    <div class="container">
        <h2 class="text-center mb-30 text-white">{{@Helper::language('best_seller')}}</h2>

        <div class="best-seller-wrapper">
            @if(count($best_seller_product) > 0)
            <div class="swiper best-seller-slider pb-30">
                <div class="swiper-wrapper">
                    @foreach ($best_seller_product as $key2=>$best_result )
                    @php
                    $product_title='';
                    if(session::get('language')==2){                        
                        $product_title = ($best_result->product_name_fr)?$best_result->product_name_fr:$best_result->product_name;
                    }else{
                        $product_title = $best_result->product_name;
                    }
                    $product_image = $best_result->get_product_images->first();
                    $product_variant = $best_result->get_product_variants->first();
                    $product_unit = Helper::getUnitById($product_variant->variant_uof);
                    
                    $fav_data = Helper::userFavoriteProduct($best_result->id);


                    // Cart
                    $gdisplay = 'none';
                    $adisplay = 'flex';
                    $is_in_cart = false;
                    $user_id = auth()->guard('user')->id();
                    $product_id = $best_result->id;
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

                                 @if ($best_result->bogo_status)
                                    <div class="mb-1 mt-1">
                                        <span class="bogo" id="bogo">
                                            {{@Helper::language('bogo')}}
                                        </span>
                                    </div>
                                @endif

                                @if ($best_result->offer_status && !$best_result->bogo_status)
                                    <div class="mb-1 mt-1">
                                        <span class="offer" id="offer">
                                            @if ($best_result->offer_type === 'flat')
                                                Flat {{ intval($best_result->discount_amount) }} {{ Helper::Settings('currency_symbol') }} Off
                                            @elseif ($best_result->offer_type === 'percentage')
                                                {{ intval($best_result->discount_amount) }}% Off
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                <a href="{{route('productdetails',['id'=>Helper::encodeUrl($best_result->id)])}}" >
                                    @if (file_exists(public_path() . '/uploads/product/'.$product_image->image))					
                                        <img src="{{ asset('uploads/product/'.$product_image->image) }}" title="{{$product_title}}" />
                                    @else
                                        <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                                    @endif 
                                </a>
                            </div>
                            <div class="bs-content">
                                <h6><a href="{{route('productdetails',['id'=>Helper::encodeUrl($best_result->id)])}}" class="heading-six">{{@ucfirst($product_title)?: ''}}</a></h6>
                                <span class="text-sm grey-text">{{@$product_variant->variant_size? $product_variant->variant_size.' '.$product_unit :''}}</span>
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

                                            if ($best_result->offer_status && !$best_result->bogo_status) {
                                                if ($best_result->offer_type === 'flat') {
                                                    $final_price = max(0, $original_price - $best_result->discount_amount);
                                                } elseif ($best_result->offer_type === 'percentage') {
                                                    $final_price = max(0, $original_price - ($original_price * $best_result->discount_amount / 100));
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


                                @if(!empty($best_result->average_rating))
                                <div class="product-rating" >
                                    <span class="text-sm black-text">{{@$best_result->average_rating?: ''}}</span>
                                    <i class="icon-star-fill" ></i>
                                </div>
                                @endif
                                {{-- <a title="{{Helper::language('add_to_cart')}}" href="{{route('productdetails',['id'=>Helper::encodeUrl($best_result->id)])}}" class="add-bucket"><i class="icon-bucket"></i></a>
                              --}}

                                @if ($available_qty>0)
                                    <a style="display: <?php echo $adisplay ; ?>;" title="{{Helper::language('add_to_cart')}}"  data-product-id="{{ Helper::encodeUrl($product_id) }}"  data-variant-id="{{ $variant_id }}" data-bogo_status="{{ $best_result->bogo_status}}" data-offer_status="{{ $best_result->offer_status}}" class="add-bucket"  href="javascript:void(0);"><i class="icon-bucket"></i></a>
                                @endif

                                </div>
                                <input class="fav-icon checked_box" type="checkbox" id="fav-item1{{$key2}}" value="{{ (isset($fav_data) && $fav_data != false) ? '1' : "0" }}" {{ (isset($fav_data) && $fav_data != false) ? 'checked' : "" }} onclick="return productFavTest({{$best_result->id}},{{ (isset($fav_data) && $fav_data != false) ? '1' : "0" }})" />
                                <label class="fav-button" title="{{Helper::language('add_to_favourite')}}" for="fav-item1{{$key2}}"></label>
                            
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="nav-btn-wrapper">
                <div class="best-seller-button-prev common-btn-prev"></div>
                <div class="best-seller-button-next common-btn-next"></div>
            </div>
            <div class="swiper-scrollbar best-seller-scrollbar common-scroll"></div>
            @else
            <h3 class="text-center mb-30 text-danger">{{@$no_data_found?:''}}</h3>
            @endif
        </div>
    </div>
</section>
@endif
<!-- End Best Seller -->

<!-- Latest Blog -->
<section class="latest-blog py-60">
    <div class="container">
        {{-- <h2 class="text-center mb-40">{{@Helper::language('latest_blog')}}</h2> --}}
        <h2 class="text-center mb-40">{{@Helper::language('event')}}</h2>
        <div class="blog-wrapper mb-40">
            <div class="row blog-row">
                @if(isset($blogs) & (count($blogs) > 0 ))
                @foreach ($blogs as $blogs_result )
                @php
                $blog_title='';
                if(session::get('language')==2){                    
                    $blog_title = ($blogs_result->title_fr)?$blogs_result->title_fr:$blogs_result->title;
                }else{
                    $blog_title = $blogs_result->title;
                }
                @endphp
                <div class="col-md-4 col-sm-6 blog-col">
                    <div class="blog-box">
                        <div class="blog-image">
                            @if (file_exists(public_path() . '/uploads/blog/'.$blogs_result->image))
                            <img src="{{ asset('uploads/blog/'.$blogs_result->image) }}" title="{{$blog_title}}" />
                            @else
                            <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                            @endif
                        </div>
                        <div class="blog-content">
                            <h5><a href="{{url('blog-details/'.Helper::encodeUrl($blogs_result->id))}}">{{$blog_title}}</a></h5>
                            <span>{!! @$blogs_result->created_at ? Carbon\Carbon::parse($blogs_result->created_at)->format(env('DATE_FORMAT', 'Y-m-d')) : "-" !!}</span>
                            <a href="{{url('blog-details/'.Helper::encodeUrl($blogs_result->id))}}" class="text-link">{{Helper::language('read_more')}}
                                <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path id="Vector" d="M5.02442 4.9663C5.02536 5.05124 5.01005 5.13552 4.97939 5.21432C4.94872 5.29313 4.90331 5.36489 4.84573 5.42551L1.11855 9.3181C1.00158 9.44027 0.842405 9.5094 0.676054 9.5103C0.509704 9.51119 0.349799 9.44377 0.231517 9.32288C0.113236 9.20198 0.0462661 9.0375 0.0453413 8.86563C0.0444166 8.69375 0.109612 8.52857 0.226586 8.4064L3.51897 4.9744L0.196093 1.57799C0.0930913 1.45508 0.0387609 1.29655 0.0439595 1.13408C0.049158 0.971613 0.113503 0.81718 0.224135 0.701639C0.334766 0.586097 0.483537 0.517959 0.640718 0.510839C0.797899 0.503719 0.951912 0.558141 1.07198 0.663232L4.84084 4.51549C4.95689 4.63508 5.02282 4.79699 5.02442 4.9663Z" fill="#2B2B2B" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <h3 class="text-center mb-30 text-danger">{{@$no_data_found?:''}}</h3>
                @endif
            </div>
        </div>
        @if(isset($blogs) & (count($blogs) > 0 ))
        <div class="btn-wrapper text-center">
            <a href="{{route('frontend.blog')}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
        </div>
        @endif
    </div>
</section>
<!-- End Latest Blog -->
<!-- Service -->
@include('frontend.service.service')
<!-- End Service -->

<!-- Newsletter -->
@include('frontend.newsletter.newsletter')
<!-- End Newsletter -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function productFavTest(product_id, status) {
        // alert('')
        var status = status;
        var user_id = $("#user_id").val();
        // alert(user_id);
        if (user_id) {
            // alert('ttt')
            action_url = "{{ route('productfav') }}";
        } else {
            // alert('ttt25')
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
                $('.loader').css("visibility", "visible");
                // return false;
                if(user_id != ''){
                    location.reload();
                }else{
                    location.href="{{route('websitelogin')}}";
                }
                },
                error:function(){
                    
            }
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
                    updateCartUI();
                    if (response.success == "true") {
                        if (typeof shakeFloatingCart === 'function') shakeFloatingCart();
                    // Add shake animation to button
                    $this.addClass('shake');
                    $this.one('animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd', function() {
                        $this.removeClass('shake');
                    });
                        // Swal.fire({
                        //     icon: "success",
                        //     title: success_message,
                        //     text: added_product_message,
                        //     customClass: {
                        //         confirmButton: 'swal-custom-confirm'
                        //     }
                        // });
                    }
                },
            });
        });
</script>

@endsection