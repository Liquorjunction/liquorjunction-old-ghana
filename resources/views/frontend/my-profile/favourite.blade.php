@extends('frontend.layouts.app')
@section('title', Helper::language('my_favourite'))
@section('content')
@include('sweetalert::alert')

<style>
        .favs-button{
        right: 0px !important;    
        }

        @media (max-width: 476px) {
        .favs-button {
            top: -300px !important;
        }
        }

       .bogo {
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

     .offer {
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

    .custom{
            height: 482px !important;
    }

    @media only screen and (max-width: 500px) {
        .custom{
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
        .custom{
                    height: 350px !important;
        }
    }

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
  
</style>


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
            <div class="col-lg-9 col-md-8">
                <h2>{{@Helper::language('my_favorite_list_label')}}</h2>  
                <div class="row product-listing-row">
                @if(count($productIds) > 0)
                    @foreach ($favourite_Product as $key=>$result )
                    <div class="col-xl-4 col-sm-6 product-listing-col custom">
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

                                <a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}">
                                    @if (file_exists(public_path() . '/uploads/product/'.$product_image->image))
                                        <img src="{{ asset('uploads/product/'.$product_image->image) }}" title="{{$product_title}}" alt="{{$product_title}}" />
                                    @else
                                        <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{$image_not_found}}" alt="{{$image_not_found}}">
                                    @endif
                                </a>
                            </div>
                            <div class="bs-content">
                                <h6><a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->id)])}}" class="heading-six">{{@ucfirst($product_title)?: ''}}</a></h6>
                                <span class="text-sm grey-text">{{@$product_variant->variant_size? $product_variant->variant_size.' '.$product_unit :''}}</span>
                                {{-- <div class="price-wrapper">
                                    @if($product_variant->variant_discounted_price=='' || $product_variant->variant_discounted_price==0)
                                        <span class="sell-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>                                        
                                    @else
                                        <span class="sell-price"> {{@$product_variant->variant_discounted_price? $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
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

                                @if(!empty($result->average_rating))
                                <div class="product-rating">
                                    <span class="text-sm black-text">{{@$result->average_rating?: ''}}</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                @endif

                                @if ($available_qty>0)
                                    <a style="display: <?php echo $adisplay ; ?>;" title="{{Helper::language('add_to_cart')}}"  data-product-id="{{ Helper::encodeUrl($product_id) }}"  data-variant-id="{{ $variant_id }}"  data-bogo_status="{{ $result->bogo_status}}" data-offer_status="{{ $result->offer_status}}" class="add-bucket"  href="javascript:void(0);"><i class="icon-bucket"></i></a>
                                @endif

                                <div class="button-wrapper" style="display: flex; align-items: center; justify-content: flex-start;">
                                    {{-- <a title="{{Helper::language('add_to_cart')}}" href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->id)]) }}" class="add-bucket" style="margin-right: 10px;">
                                        <i class="icon-bucket"></i>
                                    </a> --}}

                                    <!-- Move the fav icon next to add-to-cart button -->
                                    {{-- <input class="fav-icon" type="checkbox" id="fav-item{{$key}}" checked style="display: none;" />
                                    <label class="fav-button" title="{{Helper::language('add_to_favourite')}}" for="fav-item{{$key}}" onclick="return favorite_btn({{$result->id}},{{$user_id}})" style="cursor: pointer;margin-top:30px;margin-right:20px;"></label> --}}
                                    <input class="favs-icon" type="checkbox" id="fav-item{{$key}}" checked style="display: none;" />
                                    <label class="favs-button" title="{{Helper::language('add_to_favourite')}}" for="fav-item{{$key}}" onclick="return favorite_btn({{$result->id}},{{$user_id}})" style="cursor: pointer;"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="col-lg-12 col-sm-12">
                    <h3 class="text-danger text-center no-result-found">{{@Helper::language('no_data_found')}}</h3>
                </div>
                @endif
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
                window.location.reload();
                if (data.success == true) {}
            }
        });
    };
</script>

{{-- Add to cart --}}
<script>
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
