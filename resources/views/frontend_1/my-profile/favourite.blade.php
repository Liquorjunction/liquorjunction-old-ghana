@extends('frontend.layouts.app')
@section('title',Helper::language('my_favourite'))
@section('content')
@include('sweetalert::alert')
<div class="bread-crumb-block">
    <div class="container">
    <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{@Helper::language('my_favourite')}}</li>
        </ul>
    </div>
</div>

<section class="account pt-20 pb-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4">
                @include('frontend.layouts.account-sidebar')

                </div>
                <?php
                    //  <?php 
                    //  dd(count($productIds));
                ?>
                <div class="col-lg-9 col-md-8">
                    <h2>{{@Helper::language('my_favorite_list_label')}}</h2>  
                    <div class="row product-listing-row">
                    @if(count($productIds) > 0)
                        @foreach ($favourite_Product as $key=>$result )
                        <div class="col-xl-4 col-sm-6 product-listing-col">
                            @php
                            $product_title='';
                            if(session::get('language')==2){                            
                                $product_title = ($result->product_name_fr)?$result->product_name_fr:$result->product_name;
                                $image_not_found = 'Image non disponible';
                            }else{
                                $product_title = $result->product_name;
                                $image_not_found = 'Image not available';
                            }
                            $product_image = $result->get_product_images->first();
                            $product_variant = $result->get_product_variants->first();
                            $product_unit = Helper::getUnitById($product_variant->variant_uof);
                           
                            @endphp
                            <div class="bs-box">
                                <div class="bs-image">
                                    @if (file_exists(public_path() . '/uploads/product/'.$product_image->image))
                                    <img src="{{ asset('uploads/product/'.$product_image->image) }}" title="{{$product_title}}" alt="{{$product_title}}" />
                                    @else
                                    <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{$image_not_found}}" alt="{{$image_not_found}}">
                                    @endif
                                </div>
                                <div class="bs-content">
                                    <h6><a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->id)])}}" class="heading-six">{{@ucfirst($product_title)?: ''}}</a></h6>
                                    <span class="text-sm grey-text">{{@$product_variant->variant_size? $product_variant->variant_size.' '.$product_unit :''}}</span>
                                    <div class="price-wrapper">
                                        @if($product_variant->variant_discounted_price=='' || $product_variant->variant_discounted_price==0)
                                            <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                        @else
                                            <span class="sell-price"> {{@$product_variant->variant_discounted_price?    $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                            <span class="original-price">{{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                        @endif
                                    </div>
                                    @if(!empty($result->average_rating))
                                    <div class="product-rating">
                                        <span class="text-sm black-text">{{@$result->average_rating?: ''}}</span>
                                        <i class="icon-star-fill"></i>
                                    </div>
                                    @endif
                                    <a title="{{Helper::language('add_to_cart')}}" href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}" class="add-bucket"><i class="icon-bucket"></i></a>
                                    <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
                                </div>
                                <input class="fav-icon" type="checkbox" id="fav-item{{$key}}" checked />
                                <label class="fav-button"  title="{{Helper::language('add_to_favourite')}}" for="fav-item{{$key}}" onclick="return favorite_btn({{$result->id}},{{$user_id}})"></label>
                            </div>
                        </div>
                        <?php
                        ?>
                      
                        @endforeach
                    @else
                    <div class="col-lg-12 col-sm-12">
                        <h3 class="text-danger text-center no-result-found">{{@Helper::language('no_data_found')}}</h3>
                    </div>
                    @endif
                        {{--{{ $favorite->links('vendor.pagination.custom_pagination') }}--}}
                                                                   
                    </div>
                </div>
            </div>
        </div>
    </section>





<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script type="text/javascript">
    function favorite_btn(id, user_id) {
        
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('favorite-status')}}",
            data: {
                'id': id,
                'user_id': user_id
            },
            success: function(data) {
                // alert(data.success)
                // alert('hello')
                window.location.reload();
                if (data.success == true) {}
            }
        });
    };
</script>
@endsection