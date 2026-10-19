@extends('frontEnd.layouts.new_app')
@section('title','Product Details')
@section('content')
@include('sweetalert::alert')
<style>
    #cart_form{
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0px;
        border: none;
    }
</style>
 <script async src="https://static.addtoany.com/menu/page.js"></script>
 <?php 
        // if ($store->in_store == 1) {
        $productList =  route('productlistview',['id'=>$storeDetail->id]);


            
        // }else{

        // $productList =  route('productlistview',['id'=>$store->id]);
        // }
        ?>
<main class="site-content">
    <div class="loader" id="loader"></div> 
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><a href="{{route('store.listing-online')}}" class="text-grey body-normal">Store</a></li>
                    <li><a href="{{$productList}}" class="text-grey body-normal">{{@$storeDetail->store_name}}</a></li>
                    <li><p class="text-black body-normal">{{@$productDetail->product_name}}</p></li>
                </ul>
            </div>
        </div>
        <section class="product-details pt-30 pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-12 col-sm-12">
                        <div class="product-details-head mobile">
                            <div class="content">
                                <h3>{{@$productDetail->product_name}}</h3>
                                <p>Sold by : <a href="{{$productList}}">{{@$storeDetail->store_name}}</a></p>
                            </div>
                            <div class="action-group">
                                <div class="fav-btn">
                                    <input class="fav-icon" type="checkbox" id="mobile-fav">
                                    <label class="fav-button" for="mobile-fav"></label>
                                </div>
                                <div class="dropdown share">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="shareMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.8198 4.4047C13.3664 3.94843 12.63 3.94843 12.1766 4.4047L7.53376 9.0768C7.08036 9.53306 7.08036 10.274 7.53376 10.7303C7.98717 11.1865 8.72349 11.1865 9.1769 10.7303L11.8393 8.05113V15.7418C11.8393 16.3879 12.358 16.9099 13 16.9099C13.642 16.9099 14.1607 16.3879 14.1607 15.7418V8.05113L16.8231 10.7303C17.2765 11.1865 18.0128 11.1865 18.4662 10.7303C18.9196 10.274 18.9196 9.53306 18.4662 9.0768L13.8234 4.4047H13.8198ZM7.19643 16.9099C7.19643 16.2638 6.67773 15.7418 6.03571 15.7418C5.39369 15.7418 4.875 16.2638 4.875 16.9099V19.2459C4.875 21.1805 6.43471 22.75 8.35714 22.75H17.6429C19.5653 22.75 21.125 21.1805 21.125 19.2459V16.9099C21.125 16.2638 20.6063 15.7418 19.9643 15.7418C19.3223 15.7418 18.8036 16.2638 18.8036 16.9099V19.2459C18.8036 19.892 18.2849 20.4139 17.6429 20.4139H8.35714C7.71512 20.4139 7.19643 19.892 7.19643 19.2459V16.9099Z" fill="#96999C"/>
                                        </svg>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="shareMenuButton1">
                                        <li class="title">Share this image :</li>
                                        <li><a class="dropdown-item a2a_button_facebook" href="javascript:void(0)" target="_blank"><img src="./../assets/frontend/images/icon-fb.svg" alt="FB Icon" /></a></li>
                                        <li><a class="dropdown-item a2a_button_whatsapp" href="javascript:void(0)" target="_blank"><img src="./../assets/frontend/images/icon-wp.svg" alt="WP Icon" /></a></li>
                                    </ul>
                                </div>

       
                            </div>
                        </div>
                        <div class="xzoom-container">
                            <div class="dtc-slider">
                                <img class="xzoom lightboxed" src="{{ asset('uploads/product/').'/'.$productDetail->product_image }}" xoriginal="{{ asset('uploads/product/').'/'.$productDetail->product_image }}" rel="group1">
                            </div>
                            
                            <div class="product-details-thumbnail">
                                 <div class="swiper product-details-slider">
                                    <div class="swiper-wrapper">
                                        @foreach($productImage as $images)

                                        <div class="swiper-slide">
                                            <a href="{{ asset('uploads/product/').'/'.$images->image }}">
                                                <img class="xzoom-gallery" src="{{ asset('uploads/product/').'/'.$images->image }}" xpreview="{{ asset('uploads/product/').'/'.$images->image }}" />
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div> 
                                 <div class="swiper product-details-slider mobile">
                                    <div class="swiper-wrapper">
                                         @foreach($productImage as $images)

                                        <div class="swiper-slide">
                                            <a href="javascript:void(0)">
                                                <img class="lightboxed" src="{{ asset('uploads/product/').'/'.$images->image }}" data-link="{{ asset('uploads/product/').'/'.$images->image }}" rel="group1"/>
                                            </a>
                                        </div>
                                        @endforeach
                                       
                                    </div>
                                </div> 
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-sm-12">
                        <div class="product-details-head">
                            <div class="content">
                               <h3>{{@$productDetail->product_name}}</h3>
                                <p>Sold by : <a href="{{$productList}}">{{@$storeDetail->store_name}}</a></p>
                            </div>
                             <input type="hidden" name="user_id" id="user_id" value="{{@$user_id}}">
                            <?php
                            $fav_data = DB::table('favorite_product')->where('product_id',$productDetail->id)->where('user_id',$user_id)->where('status',1)->first();
                            ?>
                            <div class="action-group">
                                <div class="fav-btn">
                                    <input class="fav-icon" type="checkbox" id="desktop-fav" value="{{ ($fav_data != "") ? '1' : "0" }}" {{ ($fav_data != "") ? 'checked' : "" }} onclick="return productFav({{$productDetail->id}},{{ ($fav_data != "") ? '1' : "0" }})">
                                    <label class="fav-button" for="desktop-fav"></label>
                                </div>
                                <div class="dropdown share">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="shareMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.8198 4.4047C13.3664 3.94843 12.63 3.94843 12.1766 4.4047L7.53376 9.0768C7.08036 9.53306 7.08036 10.274 7.53376 10.7303C7.98717 11.1865 8.72349 11.1865 9.1769 10.7303L11.8393 8.05113V15.7418C11.8393 16.3879 12.358 16.9099 13 16.9099C13.642 16.9099 14.1607 16.3879 14.1607 15.7418V8.05113L16.8231 10.7303C17.2765 11.1865 18.0128 11.1865 18.4662 10.7303C18.9196 10.274 18.9196 9.53306 18.4662 9.0768L13.8234 4.4047H13.8198ZM7.19643 16.9099C7.19643 16.2638 6.67773 15.7418 6.03571 15.7418C5.39369 15.7418 4.875 16.2638 4.875 16.9099V19.2459C4.875 21.1805 6.43471 22.75 8.35714 22.75H17.6429C19.5653 22.75 21.125 21.1805 21.125 19.2459V16.9099C21.125 16.2638 20.6063 15.7418 19.9643 15.7418C19.3223 15.7418 18.8036 16.2638 18.8036 16.9099V19.2459C18.8036 19.892 18.2849 20.4139 17.6429 20.4139H8.35714C7.71512 20.4139 7.19643 19.892 7.19643 19.2459V16.9099Z" fill="#96999C"/>
                                        </svg>
                                    </button>
                                    <ul class="dropdown-menu a2a_kit a2a_kit_size_32 a2a_default_style" aria-labelledby="shareMenuButton1">
                                        <li class="title">Share this image :</li>
                                        <li><a class="dropdown-item a2a_button_facebook" href="javascript:void(0)" target="_blank"><img src="./../assets/frontend/images/icon-fb.svg" alt="FB Icon" /></a></li>
                                        <li><a class="dropdown-item a2a_button_whatsapp" href="javascript:void(0)" target="_blank"><img src="./../assets/frontend/images/icon-wp.svg" alt="WP Icon" /></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <p class="description">{{@$productDetail->short_description}}</p>
                        @if(!empty($user_id))
                        @if($productDetail->discount_price != 0)
                        <h2 class="price">{{@$setting->currency_symbol}}{{ isset($productDetail->discount_price) ? $productDetail->discount_price : $productDetail->retail_price}} <span class="price-disable">{{@$setting->currency_symbol}}{{@$productDetail->retail_price}}</span></h2>
                        @else
                        <h2 class="price">{{@$setting->currency_symbol}}{{$productDetail->retail_price}} <span class="price-disable">{{@$setting->currency_symbol}}{{@$productDetail->retail_price}}</span></h2>
                        @endif
                        @else
                        <a href="javascript:void(0)" class="border-btn hvr-radial-out-border product-detail-login-btn" onclick="return LoginForPrice({{@$productDetail->id}})">Login For Price</a>
                        @endif
                        @if(!empty($user_id))
                        <div class="btn-action-group">
                            <span class="counter mb-0">
                                <input class="counter__input" type="text" value="{{isset($cartData->quantity) ? $cartData->quantity : '1'}}" name="counter" max="4">
                                <a class="counter__increment" href="javascript:void(0)"><i class="fa-solid fa-plus"></i></a>
                                <a class="counter__decrement" href="javascript:void(0)"><i class="fa-solid fa-minus"></i></a>
                            </span>
                            <div class="buy-btn">
                                <!-- <a href="javascript:void(0)" class="common-btn hvr-radial-out" data-bs-toggle="modal" data-bs-target="#replaceCartItem">Add To Cart</a> -->
                                <input type="hidden" name="product_price" id="product_price" value="{{@$productDetail->retail_price}}">
                                <input type="hidden" name="discount_price" id="discount_price" value="{{@$productDetail->discount_price}}">
                                <a href="javascript:void(0)" class="common-btn hvr-radial-out" onclick="return cartAdd({{$productDetail->id}})">Add To Cart</a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#storePurchase" class="common-btn hvr-radial-out">Buy Now</a>
                                <!-- <a href="{{route('checkout')}}" class="common-btn hvr-radial-out">Buy Now</a> -->
                            </div>
                        </div>
                        @endif
                        <div class="page-border"></div>
                        <h5>Product Description</h5>
                        <!-- <div>{{@$productDetail->description}}</div> -->
                        <div>{!!html_entity_decode(@$productDetail->description)!!}</div>
                        @if($productDetail->tech_data_sheet)
                        <h6 style="margin-top: 10px;">Tech Data Sheets</h6>
                        <?php 
                        $url = env('APP_URL').'uploads/product'.'/'.$productDetail->tech_data_sheet;
                        ?>
                        <input type="text" value="{{@$productDetail->product_name}} - Data Sheet" class="file-download" readonly>
                        <a href="{{@$url}}"  target="_blank" class="small-common-btn hvr-radial-out">View Document</a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="modal replace-cart-modal fade" id="replaceCartItem" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <h3 class="text-center">Replace Cart Item?</h3>
                        <p>Your cart contains order from  fortescue metals group 
                            for online product purchase. Do you want discard the
                            selection and add order from CIMIC Group Engineer & Contractor for In - Store Purchase?
                        </p>
                        <div class="modal-btn-group">
                            <a href="javascript:void(0)" class="larg-border-btn hvr-radial-out btn" class="btn-close" data-bs-dismiss="modal" aria-label="Close">No</a>
                            <a href="cart.php" class="common-btn hvr-radial-out btn">Replace</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!--         <div class="modal replace-cart-modal fade" id="storePurchase" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <h3 class="text-center">Please Select Order Type?</h3>
                        <form class="purchase-option" id="cart_form">
                            <input type="hidden" name="product_id" id="product_id" value="{{@$productDetail->id}}">
                            <div class="radio-group">
                                <input id="radioPurchaseOne" value="2" type="hidden" name="order_type">
                                <input id="radioPurchaseOne" value="2" type="radio" name="order_type" checked /> -->
                                <!-- <label for="radioPurchaseOne">Online Purchase</label>
                            </div>
                        
                        <div class="modal-btn-group">
                            <a href="javascript:void(0)" class="larg-border-btn hvr-radial-out btn" class="btn-close" data-bs-dismiss="modal" aria-label="Close">No</a>
                            <a href="#" onclick="return BuyNow()" class="common-btn hvr-radial-out btn">Yes</a>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> --> 

        <div class="modal replace-cart-modal fade" id="storePurchase" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <h4 class="text-center">Are you sure you want to buy?</h4>
                        <form class="purchase-option" id="cart_form">
                            <input type="hidden" name="product_id" id="product_id" value="{{@$productDetail->id}}">
                            <div class="radio-group">
                                <input id="radioPurchaseOne" value="2" type="hidden" name="order_type">
                                <!-- <input id="radioPurchaseOne" value="2" type="radio" name="order_type" checked /> -->
                                <!-- <label for="radioPurchaseOne">Online Purchase</label> -->
                            </div>
                           <!--  <div class="radio-group">
                                <input id="radioPurchaseTwo" value="1" type="radio" name="order_type" checked />
                                <label for="radioPurchaseTwo">InStore Purchase</label>
                            </div> -->
                        
                        <div class="modal-btn-group">
                            <a href="javascript:void(0)" class="larg-border-btn hvr-radial-out btn" class="btn-close" data-bs-dismiss="modal" aria-label="Close">No</a>
                            <a href="#" onclick="return BuyNow()" class="common-btn hvr-radial-out btn">Yes</a>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="modal order-successfully-modal fade p-0" id="OrderSuccessfully" tabindex="-1" aria-modal="true" role="dialog">
            

            
        </div>
    <script type="text/javascript">

        function LoginForPrice(product_id)
        {
            event.preventDefault();
            localStorage.setItem('product_id',product_id);
            var url = "{{route('websitelogin')}}";
            window.location.href = url;
        }

        function productFav(product_id,status) {

        var status = status;
        var user_id = $("#user_id").val();
        if (user_id) {
        action_url = "{{ route('productfav') }}";
        }else{
            var url = "{{route('websitelogin')}}";
            window.location.href = url;
        
        }
        var csrf = "{{ csrf_token() }}";
        $.ajax({
                            url: action_url,
                            data: {'user_id':user_id,'product_id':product_id,'status':status},
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            type: "POST",
                    
                             beforeSend: function(){
                                    $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function (response) 
                            {
                                // return false;
                                location.reload();
                            },
                        });

    }

    function cartAdd(product_id)
    {

        // alert(product_id)
        var product_price = $("#product_price").val();
        var user_id = $("#user_id").val();
        var user_id = $("#user_id").val();
        var discount_price = $("#discount_price").val();
        var quantity = $(".counter__input").val();
        // alert(counter__input);

        action_url = "{{ route('productcartadd') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
            url: action_url,
            data: {'user_id':user_id,'product_id':product_id,'product_price':product_price,'quantity':quantity,'discount_price':discount_price},
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            type: "POST",
            beforeSend: function(){
                $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function (response) 
            {
                // Only shake and show success if add to cart is successful
                if (response.code == 1 || response.success) {
                    var addToCartBtn = document.querySelector('.buy-btn a.common-btn');
                    if (addToCartBtn) {
                        addToCartBtn.classList.remove('shake');
                        void addToCartBtn.offsetWidth;
                        addToCartBtn.classList.add('shake');
                    }
                    // Show success message using SweetAlert2
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Cart!',
                            text: 'The item was successfully added to your cart.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Added to Cart!');
                    }
                    // Optionally update cart UI here if needed
                } else if (response.code != 0) {
                    $('.loader').css("visibility", "visible");
                    var url = "{{route('cart')}}";
                    window.location.href = url;
                } else {
                    location.reload();
                }
            },
        });

    }

    function BuyNow()
    {
        // var order_type = $(".radio-group").val();
        var form_data = new FormData($('#cart_form')[0]);

        var quantity = $(".counter__input").val();
        // alert(quantity);
        form_data.append('quantity',quantity)

        action_url = "{{ route('buy-now') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: form_data,
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            processData: false,
                            contentType: false,
                            type: "POST",
                            dataType: 'json',
                    
                             beforeSend: function(){
                                    $(".loader").fadeIn();
                                    $('.loader').css("visibility", "visible");
                                },
                            success: function (response) 
                            {
                                // console.log('response'+response.success);
                                // var obj = JSON.parse(response);
                                // return false;
                                if (response.success == "purchase_store") {
                                    if (response.html == 1) {
                                        $('.loader').css("visibility", "hidden");
                                        location.reload();
                                    }else if(response.html == 2){
                                        $('.loader').css("visibility", "hidden");
                                        var url = "{{route('frontend.home')}}";
                                    window.location.href = url;
                                    }else{
                                         $('.loader').css("visibility", "hidden");
                                    $(document).find("#OrderSuccessfully").append(response.html);
                                $('#OrderSuccessfully').modal('show');
                                    }

                                     
                                }else{
                                    if (response.html == 1) {
                                        $('.loader').css("visibility", "hidden");
                                        location.reload();
                                    }else{
                                         var url = "{{route('checkout')}}";
                                    window.location.href = url;
                                    }
                                   
                                }
                                
                                // location.reload();
                            },
                        });

        
    }
    </script>
@endsection