@extends('frontend.layouts.app')
@section('title','Home')
@section('content')
@include('sweetalert::alert')
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
                                       
                                        @if($banner_result->type ==1 && $banner_result->category_id!="" )
                                            <a href="{{route('productlist',['id' => Helper::encodeUrl($banner_result->category_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
                                        @elseif($banner_result->type ==2 &&$banner_result->product_id!="")
                                            <a href="{{route('productdetails',['id' => Helper::encodeUrl($banner_result->product_id)])}}" class="solid-button">{{@Helper::language('shop_now')}}</a>
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
<section class="shop-by-spirit py-60">
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
            <div class="nav-btn-wrapper">
                <div class="shop-spirit-button-prev common-btn-prev"></div>
                <div class="shop-spirit-button-next common-btn-next"></div>
            </div>
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
        @if($banners_offer->type ==1 && $banners_offer->category_id!="" )
        <a href="{{route('productlist',['id' => Helper::encodeUrl($banners_offer->category_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
        @elseif($banners_offer->type ==2 &&$banners_offer->product_id!="")
        <a href="{{route('productdetails',['id' => Helper::encodeUrl($banners_offer->product_id)])}}" class="solid-button">{{@Helper::language('order_now')}}</a>
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
                    @foreach ($offer_product as $offer_result )
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
                    @endphp
                    <div class="swiper-slide">
                        <div class="offers-box">
                            <div class="offers-image">
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
                                <div class="price-wrapper">
                                    @if($product_variant->variant_discounted_price=='' || $product_variant->variant_discounted_price==0)
                                        <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                    @else
                                        <span class="sell-price"> {{@$product_variant->variant_discounted_price?    $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                        <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                       
                                    @endif
                                </div>
                                @if(!empty($offer_result->average_rating))
                                <div class="product-rating">
                                    <span class="text-sm black-text">{{@$offer_result->average_rating?: ''}}</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                @endif
                                <a title="{{Helper::language('add_to_cart')}}" href="{{ route('productdetails', ['id' => Helper::encodeUrl($offer_result->id)]) }}" class="add-bucket"><i class="icon-bucket"></i></a>
                            </div>
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
                    <div class="swiper-slide">
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
                                <span style="color: {{ $highlight_result->text_color }};">{{ $banner_title }}</span>
                                <h3 style="color: {{ $highlight_result->text_color }};">{{ $banner_description }}</h3> 
                                {{--<a href="{{$highlight_result->banner_url}}" class="solid-button">{{@Helper::language('order_now')}}</a>--}}
                                <?php
                                //  dd($highlight_result->category_id );
                                ?>
                                @if($highlight_result->type ==1 && $highlight_result->category_id!="" )
                                <a href="{{route('productlist',['id' => Helper::encodeUrl($highlight_result->category_id)])}}" class="solid-button">{{@Helper::language('explore_more')}}</a>
                                @elseif($highlight_result->type ==2 &&$highlight_result->product_id!="")
                                <a href="{{route('productdetails',['id' => Helper::encodeUrl($highlight_result->product_id)])}}" class="solid-button">{{@Helper::language('order_now')}}</a>
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

                    @endphp
                    <div class="swiper-slide">
                        <div class="bs-box">
                            <div class="bs-image">
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
                                <div class="price-wrapper">
                                    @if($product_variant->variant_discounted_price=='' || $product_variant->variant_discounted_price==0)
                                        <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                    @else
                                        <span class="sell-price"> {{@$product_variant->variant_discounted_price?    $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                        <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                    @endif

                                </div>
                                @if(!empty($best_result->average_rating))
                                <div class="product-rating" >
                                    <span class="text-sm black-text">{{@$best_result->average_rating?: ''}}</span>
                                    <i class="icon-star-fill" ></i>
                                </div>
                                @endif
                                <a title="{{Helper::language('add_to_cart')}}" href="{{route('productdetails',['id'=>Helper::encodeUrl($best_result->id)])}}" class="add-bucket"><i class="icon-bucket"></i></a>
                                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
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
        <h2 class="text-center mb-40">{{@Helper::language('latest_blog')}}</h2>
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
@endsection