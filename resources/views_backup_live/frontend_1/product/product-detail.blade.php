@extends('frontend.layouts.app')
@section('title', 'Product List')
@section('content')
<style>
/* video::-webkit-media-controls, video::-moz-media-controls, video::-o-media-controls, video::-ms-media-controls {   display: none !important; } */
video::-webkit-media-controls-timeline {
    display: none;
}
</style>
    @include('sweetalert::alert')
    @php
        if (session::get('language') == 1) {
            $product_title = $product_info->product_name;
            $category_title = $product_info->get_category->title;
            $product_short_description = $product_info->short_description;
            $product_long_description = $product_info->description;
            $brand_name = $product_info->get_brand_details->title;
        } else {
            $product_title = @isset($product_info->product_name_fr) ? $product_info->product_name_fr : $product_info->product_name;
            $product_short_description = $product_info->short_description_fr ? $product_info->short_description_fr : $product_info->short_description;
            $product_long_description = $product_info->page_content_fr ? $product_info->page_content_fr : $product_info->description;
            $category_title = $product_info->get_category->title_fr ? $product_info->get_category->title_fr : $product_info->get_category->title;
            $brand_name = $product_info->get_brand_details->title_fr ? $product_info->get_brand_details->title_fr : $product_info->get_brand_details->title;
        }
        $user_id = isset(
            auth()
                ->guard('user')
                ->user()->id,
        )
            ? auth()
                ->guard('user')
                ->user()->id
            : '';
        $product_id = $product_info->id;
    @endphp
   
    <div class="loader" id="loader"></div>
    <div class="bread-crumb-block">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">{{ @Helper::language('home') }}</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('productlist', ['id' => Helper::encodeUrl($product_info->category_id)]) }}">{{ $category_title }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product_title }} </li>
                </ul>
            </nav>
        </div>
    </div>
    <input type="hidden" value="{{ $product_info->id }}" id="product_ids">
    <input type="hidden" value="{{ Helper::encodeUrl($product_info->id) }}" id="prod_ids">
    <section class="product-details pt-20 pb-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="product-details-left">
                        <div class="product-details-head mobile">
                            {{-- <span class="title-two grey-text">MS Rosé test</span>
                        <h2>Rose of Sangiovese two line goes here</h2>
                        <div class="product-pricing text-start">
                            <h5>168 GH₵<span>268 GH₵</span></h5>
                        </div>
                        <div class="product-rating">
                            <span class="text-sm black-text">4.2</span>
                            <i class="icon-star-fill"></i>
                        </div> --}}
                        </div>
                        <div class="product-detail-main-box">
                            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                                class="swiper product-detail">
                                <div class="swiper-wrapper">
                                <!-- <div class="zoom-controls">
                <button id="zoom-in">+</button>
                <button id="zoom-out">-</button>
            </div> -->
                                    @foreach ($product_info->get_product_images as $result)
                                        <div class="swiper-slide">
                
                                            <div class="product-img-block">
                                            <a href="{{ asset('uploads/product/' . $result->image) }}" class="lightbox">
                            <img class="xzoom" src="{{ asset('uploads/product/' . $result->image) }}" xoriginal="{{ asset('uploads/product/' . $result->image) }}" />
                        </a>
                                            <!-- <img class="xzoom" src="{{ asset('uploads/product/' . $result->image) }}"
                                            xoriginal="{{ asset('uploads/product/' . $result->image) }}" /> -->
                                            <!-- <img class="zoom-img" src="{{ asset('uploads/product/' . $result->image) }}" data-zoom-level="1" /> -->
                                            <!-- <img class="zoom-img" id="zoom_{{ $loop->index }}" src="{{ asset('uploads/product/' . $result->image) }}" data-zoom-image="{{ asset('uploads/product/' . $result->image) }}" /> -->

                                            </div>
                                        </div>
                                    @endforeach
                                    @if (!empty($product_info->video) && file_exists(public_path() . '/uploads/product/' . $product_info->video))
                                        <div class="swiper-slide">
                                            <div class="product-img-block video">
                                                <!-- <iframe id="player0"
                                                    src="{{ asset('uploads/product/' . $product_info->video) }}"frameborder="0"
                                                    allowfullscreen iframe-video></iframe> -->
                                                    <video src="{{ asset('uploads/product/'.$product_info->video) }}" autoplay muted></video>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="product-detail-next common-btn-next"></div>
                                <div class="product-detail-prev common-btn-prev"></div>
                                <span class="available" id="available">
                                    @if ($product_info->get_product_variants[0]->variant_qty > 0)
                                        {{ 'Available' }}
                                    @else
                                        {{ 'Sold Out' }}
                                    @endif
                                </span>
                            </div>
                            <div thumbsSlider="" class="swiper product-detail-thumb">
                                <div class="swiper-wrapper">
                                    @foreach ($product_info->get_product_images as $result)
                                        <div class="swiper-slide">
                                            <div class="product-img-thumb-block">
                                                <img src="{{ asset('uploads/product/' . $result->image) }}"
                                                    style="height:150px;width:170px;" />
                                            </div>
                                        </div>
                                    @endforeach
                                    @if (!empty($product_info->video) && file_exists(public_path() . '/uploads/product/' . $product_info->video))
                                        <div class="swiper-slide">
                                            <div class="product-img-thumb-block play">
                                                <!-- <iframe id="player0"
                                                    src="{{ asset('uploads/product/' . $product_info->video) }}?controls=0"
                                                    frameborder="0" allowfullscreen=""  iframe-video controls="0"> </iframe> -->
                                                <video src="{{ asset('uploads/product/'.$product_info->video) }}"></video>
                                        <span class="play-video"><i class="icon-play"></i></span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="swiper product-details-slider-mobile">
                            <div class="swiper-wrapper">
                                @foreach ($product_info->get_product_images as $result)
                                    <div class="swiper-slide">
                                        <div class="product-img-block">
                                        <a href="{{ asset('uploads/product/' . $result->image) }}" class="lightbox">
                        <img class="xzoom" src="{{ asset('uploads/product/' . $result->image) }}" />
                    </a>
                                        <!-- <img class="xzoom" src="{{ asset('uploads/product/' . $result->image) }}" /> -->

                                            <!-- <img class="zoom-img" src="{{ asset('uploads/product/' . $result->image) }}" /> -->
                                        </div>
                                    </div>
                                @endforeach
                                @if (!empty($product_info->video) && file_exists(public_path() . '/uploads/product/' . $product_info->video))
                                    <div class="swiper-slide">
                                        <div class="product-img-block video">
                                            <iframe id="player0"
                                                src="{{ asset('uploads/product/' . $product_info->video) }}" frameborder="0"
                                                allowfullscreen iframe-video></iframe>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="product-detail-next-mobile common-btn-next"></div>
                            <div class="product-detail-prev-mobile common-btn-prev"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-md-12 col-sm-12">
                    <form action="{{ route('checkout') }}" method="post">
                        <div class="product-details-head">
                            <span class="title-two grey-text">{{ ucfirst($category_title) }}</span>
                            <h2>{{ ucfirst($product_title) }}</h2>
                            <div class="product-pricing text-start">
                                <h5>
                                    <originalprice id="discounted-price">
                                        @if (
                                            $product_info->get_product_variants[0]->variant_discounted_price == '' ||
                                                $product_info->get_product_variants[0]->variant_discounted_price == 0)
                                            {{ $product_info->get_product_variants[0]->variant_price }}
                                            {{ Helper::Settings('currency_symbol') }}
                                        @else
                                            {{ $product_info->get_product_variants[0]->variant_discounted_price }}
                                            {{ Helper::Settings('currency_symbol') }}
                                        @endif
                                    </originalprice>
                                    @if (
                                        $product_info->get_product_variants[0]->variant_discounted_price != '' &&
                                            $product_info->get_product_variants[0]->variant_discounted_price != 0)
                                        <span id="orignal-price">
                                            {{ $product_info->get_product_variants[0]->variant_price }}
                                            {{ Helper::Settings('currency_symbol') }}
                                        </span>
                                    @endif
                                </h5>
                                <input type="hidden" value="" id="variant_disc_price">
                                <input type="hidden" value="" id="varinat_orignal_price">
                            </div>
                            <?php
                            //$rating = \Helper::avrageRating($id);
                            ?>
                           @if (!empty($product_info->average_rating))
                            <div class="product-rating">
                                <span class="text-sm black-text">{{ @$product_info->average_rating }}</span>
                                <i class="icon-star-fill"></i>
                            </div>
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <label for=""
                                class="fw-normal mb-0">{{ @Helper::language('select_pack_size') }}</label>
                            <div class="pack-size">
                                @if ($product_info->get_product_variants)
                                    @foreach ($product_info->get_product_variants as $key => $variant_result)
                                        <div class="check-group">
                                            <input class="form-check-input" type="radio"
                                                data-id="{{ Helper::encodeUrl($variant_result->id) }}" name="pack_size"
                                                id="pack-size{{ Helper::encodeUrl($variant_result->id) }}"
                                                @if ($key == 0) checked="checked" @endif
                                                value="{{ $variant_result->id }}">
                                            <label for="pack-size-1"
                                                onclick="varinatPice('{{ Helper::encodeUrl($variant_result->id) }}');">
                                                {{ @$variant_result->variant_size ?: '' }}
                                                {{ Helper::getUnitById($variant_result->variant_uof) }}</label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <?php
                        $style = 'none';
                        if ($product_info->get_product_variants[0]->available_qty > 0) {
                            $style = 'block';
                        }
                        $varinat_qty = 1;
                        if (!empty($cartData) && $cartData->quantity!="") {
                            $varinat_qty = $cartData->quantity;
                        }else if(@Helper::getCartQuantity($product_info->get_product_variants[0]->id)){
                            $varinat_qty = Helper::getCartQuantity($product_info->get_product_variants[0]->id);
                        }
                        ?>
                        <div style="display: <?php echo $style; ?>" id="div-cart">
                            <div class="btn-action-group">
                                <span class="counter mb-0">

                                    <input class="counter__input" type="text" value="{{@$varinat_qty}}" name="counter"
                                        size="5" readonly="readonly" />
                                    <a class="counter__increment" href="javascript:void(0)">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g id="plus">
                                                <path id="Vector"
                                                    d="M19.1999 11.2H12.8V4.79995C12.8 4.35845 12.4416 4 11.9999 4C11.5584 4 11.2 4.35845 11.2 4.79995V11.2H4.79995C4.35845 11.2 4 11.5584 4 11.9999C4 12.4416 4.35845 12.8 4.79995 12.8H11.2V19.1999C11.2 19.6416 11.5584 20 11.9999 20C12.4416 20 12.8 19.6416 12.8 19.1999V12.8H19.1999C19.6416 12.8 20 12.4416 20 11.9999C20 11.5584 19.6416 11.2 19.1999 11.2Z"
                                                    fill="#242424" />
                                            </g>
                                        </svg>
                                    </a>
                                    <a class="counter__decrement" href="javascript:void(0)">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M4.95996 12.9203H19.0399C19.5702 12.9203 20 12.4905 20 11.9601C20 11.4298 19.5703 11 19.0399 11H4.95996C4.4298 11.0001 4 11.4299 4 11.9602C4 12.4905 4.4298 12.9203 4.95996 12.9203Z"
                                                fill="#242424" />
                                        </svg>
                                    </a>
                                </span>
                                <?php
                                // dd($product_info);
                                ?>
                                {{-- <a @if (isset(auth()->user('main_user')->id) && auth()->user('main_user')->id != '') onclick="cartAdd('{{Helper::encodeUrl($product_info->id)}}');" @else href="{{route('websitelogin')}}" @endif class="solid-button icon-border-btn" id="add-to-bucket"><i class="icon-cart"></i>Add to bucket</a> --}}
                                {{-- <a data-id="{{ Helper::encodeUrl($product_info->id) }}"
                                    class="solid-button icon-border-btn" id="add-to-bucket"><i
                                        class="icon-bucket"></i>{{ @Helper::language('add_to_bucket') }}</a> --}}
                                @php
                                $gdisplay = 'none';
                                $adisplay = 'flex';
                                $is_in_cart = false;
                                $get_cart_array = Session::get('cart_info');
                                if(isset($get_cart_array)){           
                                    if(array_key_exists($product_info->id,$get_cart_array)){
                                        foreach($get_cart_array as $key => $variant_array){   
                                            if($key==$product_info->id && array_key_exists($product_info->get_product_variants[0]->id,$variant_array)){                       
                                                $is_in_cart = true;
                                            }
                                        }
                                    }
                                }else{                                    
                                    if(isset(auth()->guard('user')->user()->id) && (!empty($cartData)) ){
                                        $is_in_cart = true;
                                    }
                                }                                    
                                // if((!empty(Session::get('cart_info')) && array_key_exists($product_info->id,Session::get('cart_info') ) ) || (isset(auth()->guard('user')->user()->id) && (!empty($cartData)) ) ){                                   
                                //     $gdisplay = 'flex';
                                //     $adisplay = 'none';
                                // }
                                if($is_in_cart==true){                                   
                                    $gdisplay = 'flex';
                                    $adisplay = 'none';
                                }
                                @endphp 
                                <a style="display: <?php echo $adisplay ; ?>;" data-id="{{ Helper::encodeUrl($product_info->id) }}" class="solid-button icon-border-btn" id="add-to-bucket"><i
                                        class="icon-bucket"></i>{{ @Helper::language('add_to_bucket') }}</a>
                                                                
                                <a style="display: <?php echo $gdisplay ; ?>;" data-id="{{ Helper::encodeUrl($product_info->id) }}" href="{{route('cart')}}" class="solid-button icon-border-btn" id="go-to-cart" ><i class="icon-bucket"></i> Go to cart</a>
                            </div>
                            <div class="btn-action-group g-16">
                                {{-- <a  @if (!isset(auth()->user('main_user')->id)) onclick="" @else href="{{route('websitelogin')}}" @endif id="buy-now" class="solid-button buy-now">Buy Now</a> --}}
                                <button id="buy-now" type="submit"
                                    class="solid-button buy-now">{{ @Helper::language('buy_now') }}</button>
                                @php
                                    $favData = \DB::table('favorite_product')
                                        ->where('user_id', $user_id)
                                        ->where('product_id', $product_id)
                                        ->where('status', 1)
                                        ->first();
                                @endphp
                                {{-- <a href="#" class="icon-link-btn"><i class="icon-save"></i>Save for later</a> --}}
                                <div class="action-group">
                                <div class="icon-link-btn">
                                    <input class="fav-icon checked_box" type="checkbox" id="desktop-fav" value="{{ ($favData != "") ? '1' : "0" }}" {{ ($favData != "") ? 'checked' : "" }} onclick="return productFav({{$product_info->id}},{{ ($favData != "") ? '1' : "0" }})">
                                    <label class="fav-button" id="fav-btn"for="desktop-fav" style="position:relative; margin-left:20px; margin-top
                                    :-30px !important; height:50px;"></label><span class="label" style="margin-right:-20px;">{{ @Helper::language('add_to_wish_list') }}</span>                                    
                                </div>
                            </div>
                        </div>
                        <div class="page-border"></div>
                        <div class="product-details-content">
                            <h5>{{ @Helper::language('brand') }}</h5>
                            <p>@php echo $brand_name; @endphp</p>
                            <h5>{{ @Helper::language('product_description') }}</h5>
                            <p>{{ $product_short_description }}</p>
                            <div class="hidden-content">
                                {!! html_entity_decode($product_long_description) !!}
                            </div>
                            <a class="icon-link-btn read-more">{{ @Helper::language('read_more') }}</a>
                        </div>
                        <div class="page-border"></div>
                        <div class="product-details-review">
                            @if (isset($productRating) && count($productRating) > 0)
                                <h5>{{ @Helper::language('reviews') }}</h5>
                                <?php 
                            $i=0;
                            foreach ($productRating as $result){
                                if($i<=2){
                                    $rating=round($result->ratings);
                            ?>
                                <div class="review-block">
                                    <div class="review-star-rating">
                                        @for ($x = 1; $x <= $rating; $x++)
                                            <i class="icon-star-fill"></i>
                                        @endfor
                                    </div>
                                    <span
                                        class="text-sm">{{ @$result->first_name ? $result->first_name . ' ' . $result->last_name : '' }}
                                        {!! @$result->created_at
                                            ? ', ' . Carbon\Carbon::parse($result->created_at)->format(env('DATE_FORMAT', 'Y-m-d'))
                                            : '' !!}</span>
                                    <p>{{ $result->review }}</p>
                                </div>
                                <?php
                                    $i++;
                                }
                            }
                            ?>
                            @endif
                            @if ($productRating && count($productRating) > 3)
                                <button type="button" class="link-btn" data-bs-toggle="modal"
                                    data-bs-target="#reviewModal">{{ @Helper::language('view_all') }}
                                    {{ count($productRating) }} {{ @Helper::language('reviews') }}</button>
                            @endif
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </section>
   
    @if (isset($relatedProduct) && count($relatedProduct) > 0)
    <!-- Related Products -->
    <section class="best-seller py-60">
        <div class="container">
            <h2 class="mb-30">{{ @Helper::language('related_products') }}</h2>

            <div class="best-seller-wrapper">
                @if (isset($relatedProduct) && count($relatedProduct) > 0)
                    <div class="swiper best-seller-slider recently-viewed-slider pb-30">
                        <div class="swiper-wrapper">


                            @foreach ($relatedProduct as $key => $result)
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
                                        <a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->id)])}}" class="bs-image">
                                            
                                            @if (file_exists(public_path() . '/uploads/product/' . $product_image->image))
                                                <img src="{{ asset('uploads/product/' . $product_image->image) }}"
                                                    title="{{ $product_title }}" alt="{{ $product_title }}" />
                                            @else
                                                <img src="{{ asset('assets/frontend/images/image-not-avilable.png') }}"
                                                    title="{{ Helper::language('image_not_available') }}"
                                                    alt="{{ Helper::language('image_not_available') }}">
                                            @endif
                                        </a>
                                        <div class="bs-content">
                                            <h6><a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}"
                                                    class="heading-six">{{ @ucfirst($product_title) ?: '' }}</a></h6>
                                            <span
                                                class="text-sm grey-text">{{ @$product_variant->variant_size ? $product_variant->variant_size . ' ' . $product_unit : '' }}</span>
                                            <div class="price-wrapper">
                                                @if ($product_variant->variant_discounted_price == '' || $product_variant->variant_discounted_price == 0)
                                                    <span class="sell-price">
                                                        {{ @$product_variant->variant_price ? $product_variant->variant_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                @else
                                                    <span class="sell-price">
                                                        {{ @$product_variant->variant_discounted_price ? $product_variant->variant_discounted_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                    <span
                                                        class="original-price">{{ @$product_variant->variant_price ? $product_variant->variant_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                @endif
                                            </div>
                                            @if (!empty($result->average_rating))
                                                <div class="product-rating">
                                                    <span
                                                        class="text-sm black-text">{{ @$result->average_rating ?: '' }}</span>
                                                    <i class="icon-star-fill"></i>
                                                </div>
                                            @endif
                                            <a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}"
                                                class="add-bucket" title="{{Helper::language('add_to_cart')}}"><i class="icon-bucket"></i></a>
                                            <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->

                                        </div>
                                     
                                        <input class="fav-icon checked_box" type="checkbox"
                                            id="fav-item{{ $key }}"
                                            value="{{ isset($fav_data) && $fav_data != false ? '1' : '0' }}"
                                            {{ isset($fav_data) && $fav_data != false ? 'checked' : '' }}
                                            onclick="return productFav({{ $result->id }},{{ isset($fav_data) && $fav_data != false ? '1' : '0' }})" />
                                        <label class="fav-button"  title="{{Helper::language('add_to_favourite')}}" for="fav-item{{ $key }}"></label>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="nav-btn-wrapper">
                        <div class="recently-viewed-button-prev common-btn-prev"></div>
                        <div class="recently-viewed-button-next common-btn-next"></div>
                    </div>

                    <div class="swiper-scrollbar best-seller-scrollbar recently-viewed-scrollbar common-scroll"></div>
                @else
                    <h3 class="text-center mb-30 text-danger">{{ @$no_data_found ?: '' }}</h3>
                @endif
            </div>
        </div>
    </section>
@endif

    <!-- End Related Products -->
    <!-- Recommended Products -->
    @if ($recommendedProduct && count($recommendedProduct) > 0)
        <section class="best-seller py-60">
            <div class="container">
                <h2 class="mb-30">{{ @Helper::language('recommended_products') }}</h2>

                <div class="best-seller-wrapper">
                    @if ($recommendedProduct && count($recommendedProduct) > 0)
                        <div class="swiper best-seller-slider top-selling-slider pb-30">
                            <div class="swiper-wrapper">
                                @foreach ($recommendedProduct as $key2 => $result)
                                    @php
                                    $favData = Helper::userFavoriteProduct($result->id);
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
                                                            title="{{ Helper::language('image_not_available') }}"
                                                            alt="{{ Helper::language('image_not_available') }}">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="bs-content">
                                                <h6><a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}"
                                                        class="heading-six">{{ @ucfirst($product_title) ?: '' }}</a></h6>
                                                <span
                                                    class="text-sm grey-text">{{ @$product_variant->variant_size ? $product_variant->variant_size . ' ' . $product_unit : '' }}</span>
                                                <div class="price-wrapper">
                                                    {{-- <span class="sell-price">{{@$product_variant->variant_discounted_price? $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                    <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span> --}}
                                                    @if ($product_variant->variant_discounted_price == '' || $product_variant->variant_discounted_price == 0)
                                                        <span class="sell-price">
                                                            {{ @$product_variant->variant_price ? $product_variant->variant_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                    @else
                                                        <span class="sell-price">
                                                            {{ @$product_variant->variant_discounted_price ? $product_variant->variant_discounted_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                        <span
                                                            class="original-price">{{ @$product_variant->variant_price ? $product_variant->variant_price . Helper::Settings('currency_symbol') : '' }}</span>
                                                    @endif
                                                </div>
                                                @if (!empty($result->average_rating))
                                                    <div class="product-rating">
                                                        <span
                                                            class="text-sm black-text">{{ @$result->average_rating ?: '' }}</span>
                                                        <i class="icon-star-fill"></i>
                                                    </div>
                                                @endif
                                                <a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}"
                                                    class="add-bucket" title="{{Helper::language('add_to_cart')}}"><i class="icon-bucket"></i></a>
                                                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
                                            </div>
                                           
                                                <input class="fav-icon checked_box" type="checkbox"
                                                id="fav-item2{{ $key2 }}"
                                                value="{{ isset($favData) && $favData != false ? '1' : '0' }}"
                                                {{ isset($favData) && $favData != false ? 'checked' : '' }}
                                                onclick="return productFav({{ $result->id }},{{ isset($favData) && $favData != false ? '1' : '0' }})" />
                                            <label class="fav-button" title="{{Helper::language('add_to_favourite')}}" for="fav-item2{{ $key2 }}"></label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="nav-btn-wrapper">
                            <div class="top-selling-button-prev common-btn-prev"></div>
                            <div class="top-selling-button-next common-btn-next"></div>
                        </div>
                        <div class="swiper-scrollbar best-seller-scrollbar top-selling-scrollbar common-scroll"></div>
                    @endif
                </div>
            </div>
        </section>
    @endif
    <!-- End Recommended Products -->


    <div class="modal review-modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="icon-cross-black"></i></button>
                </div>
                <div class="modal-body">
                    <h4 class="text-center">{{ @Helper::language('reviews') }}</h4>
                    <div class="review-wrapper">
                        @if ($productRating)
                            @foreach ($productRating as $result)
                                @php
                                    $rating = round($result->ratings);
                                @endphp
                                <div class="review-block">
                                    <div class="review-star-rating">
                                        @for ($x = 1; $x <= $rating; $x++)
                                            <i class="icon-star-fill"></i>
                                        @endfor
                                    </div>
                                    <span class="text-sm">{{ $result->first_name . ' ' . $result->last_name }},
                                        {!! @$result->created_at ? Carbon\Carbon::parse($result->created_at)->format(env('DATE_FORMAT', 'Y-m-d')) : '-' !!}</span>
                                    <p>{{ $result->review }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    var read_more_lang = "{{\Helper::language('product_read_more')}}";
    var read_less_lang = "{{\Helper::language('product_read_less')}}";
</script>
@push('after-scripts')
 <!-- Include jQuery -->
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- Include elevateZoom library -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/elevatezoom/3.0.8/jquery.elevatezoom.min.js"></script>
    <script>
        function varinatPice(id) {
            $("input[name='pack_size']").removeAttr('checked');;
            $("#pack-size" + id).attr('checked', 'checked');
            $.ajax({
                url: "{{ route('productVariantPrice') }}?variantId=" + id,
                success: function(response) {                   
                    if (response.discounted_price != "" && response.discounted_price != "0.00" && response.discounted_price != "0") {
                        $("#discounted-price").text(response.discounted_price +
                        " {{ Helper::Settings('currency_symbol') }}");

                        $("#orignal-price").text(response.orignal_price +
                            " {{ Helper::Settings('currency_symbol') }}");
                    } else {
                        $("#discounted-price").text(response.orignal_price +
                        " {{ Helper::Settings('currency_symbol') }}");
                        $("#orignal-price").text("");
                    }
                    $("#varinat_orignal_price").val(response.orignal_price);
                    $("#variant_disc_price").val(response.discounted_price);
                    if (response.product_stock == true) {
                        $("#available").text('Available');
                        $("#div-cart").css("display", "block");
                        $("#add-to-bucket").css({
                            'pointer-events': ''
                        });
                        $("#buy-now").css({
                            'pointer-events': ''
                        });
                    } else {
                        $("#available").text('Sold Out');
                        $("#div-cart").css("display", "none");
                        $("#add-to-bucket").css({
                            'pointer-events': 'none'
                        });
                        $("#buy-now").css({
                            'pointer-events': 'none'
                        });
                    }
                    if (response.is_in_cart == true) {
                        $("#go-to-cart").show();
                        $("#add-to-bucket").hide();
                    }else{
                        $("#go-to-cart").hide();
                        $("#add-to-bucket").show();
                    }
                }
            });
        }

        function productFav(product_id, status) {
            var status = status;
            var user_id = $("#user_id").val();
            if (user_id) {
                action_url = "{{ route('productfav') }}";
            } else {
                var url = "{{ route('websitelogin') }}";
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
                    if (user_id != '') {
                        location.reload();
                    } else {
                        location.href = "{{ route('websitelogin') }}";
                    }
                },
            });

        }

        function cartAdd(product_id) {
            var quantity = $(".counter__input").val();
            var variantId = $("input[name='pack_size']:checked").val();
            var action_url = "{{ route('productcartadd') }}";
            var csrf = "{{ csrf_token() }}";
            //return false;
            $.ajax({
                url: action_url,
                data: {
                    'product_id': product_id,
                    'quantity': quantity,
                    'variantId': variantId
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
                    // if (response.code != 0) {
                    //     $('.loader').css("visibility", "visible");
                    //     var url = "{{ route('cart') }}";
                    //     window.location.href = url;
                    // }else{
                    //     location.reload();
                    // }
                },
            });
        }

        $('#add-to-bucket').on("click", function(e) {
            $(this).hide();
            $("#go-to-cart").show();
            var product_id = $("#prod_ids").val();
            var quantity = $(".counter__input").val();
            var variantId = $("input[name='pack_size']:checked").val();
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
                    'variantId': variantId
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
                    console.log(response);
                    $('.loader').css("visibility", "hidden");
                    $('#cart-url').removeAttr("onclick").attr('href', '{{ route('cart') }}');
                    // var totalCount = (currentCount + response.cart_count)
                    $(".cart-item-total-count").html(response.cart_count);
                    if (response.success == "true") {
                        Swal.fire({
                            icon: "success",
                            title: success_message,
                            text: added_product_message,
                        });
                    }
                },
            });
        });
    $(document).ready(function(){
        
    });

    document.querySelectorAll('.lightbox').forEach(item => {
    item.addEventListener('click', function(event) {
        event.preventDefault();
        const imageUrl = this.getAttribute('href');
        const overlay = document.createElement('div');
        overlay.classList.add('lightbox-overlay');

        const img = document.createElement('img');
        img.src = imageUrl;
        overlay.appendChild(img);

        document.body.appendChild(overlay);

        overlay.addEventListener('click', function() {
            overlay.classList.remove('active');
            setTimeout(() => {
                document.body.removeChild(overlay);
            }, 300);
        });

        setTimeout(() => {
            overlay.classList.add('active');
        }, 10);
    });
});

    
//     $(document).ready(function() {
//     $('.xzoom, .xzoom-gallery').xzoom({
//         zoomWidth: 400,
//         tint: '#333',
//         Xoffset: 15,
//         position: 'lens',
//         lensShape: 'box',
//         lens: false
//     });

//     var zoomLevel = 1;
//     $('#zoom-in').on('click', function() {
//         zoomLevel += 0.1;
//         $('.xzoom').css('transform', 'scale(' + zoomLevel + ')');
//     });

//     $('#zoom-out').on('click', function() {
//         zoomLevel -= 0.1;
//         if (zoomLevel < 1) zoomLevel = 1; // Prevent zooming out too much
//         $('.xzoom').css('transform', 'scale(' + zoomLevel + ')');
//     });
// });
// $(document).ready(function() {
//       $('.zoom-img').each(function() {
//         $(this).elevateZoom({
//           zoomType: "inner",
//           cursor: "zoom-in",
//           scrollZoom: true
//         });
//       });
//     });
    const test = $('iframe video') ;
    if(test){
        //alert();
        //test.style.webkitMediaControlsTimeline= 'display: none;';
    }
    </script>
@endpush
