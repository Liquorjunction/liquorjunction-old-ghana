@extends('frontend.layouts.app')
@section('title','Product List')
@section('content')
@include('sweetalert::alert')

<div class="bread-crumb-block">
    <div class="container">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Cart</li>
        </ul>
    </div>
</div>
<!-- Cart -->
<section class="cart pt-20">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="cart-top">
                    <h2 class="mb-0">My Cart</h2>
                    <a href="{{route('frontend.home')}}" class="link-button">Add items</a>                            
                </div>
                <div class="cart-item">
                    @if($productData && count($productData) > 0)
                    <ul class="cart-item-lists">
                       
                        @foreach ($productData as $result)
                        @php
                            $product_title='';
                            if(session::get('language')==1){
                                $product_title = $result->product_variants->get_product_details->product_name;
                                $image_not_found = 'Image not available';
                            }else{
                                $product_title = ($result->product_variants->get_product_details->product_name_fr)?$result->product_variants->get_product_details->product_name_fr:$result->product_variants->get_product_details->product_name;
                                $image_not_found = 'Image non disponible';
                            }   
                            $product_image = $result->product_variants->get_product_details->get_product_images->first();
                           // $product_variant = $result->get_product_details->get_product_variants->first();              
                            $product_unit = Helper::getUnitById($result->product_variants->variant_uof);
                        @endphp
                        <li>
                            <div class="single-product">
                                <div class="product-img">                                            
                                    <a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->product_variants->get_product_details->id)])}}">
                                        @if (file_exists(public_path() . '/uploads/product/'.$product_image->image))					
                                        <img src="{{ asset('uploads/product/'.$product_image->image) }}" title="{{$product_title}}" alt="{{$product_title}}" />
                                        @else 
                                        <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{$image_not_found}}" alt="{{$image_not_found}}">
                                        @endif 
                                    </a>
                                </div>
                                <div class="product-detail">
                                    <h6 class="mb-1 title-one"><a href="{{route('productdetails',['id'=>Helper::encodeUrl($result->product_variants->get_product_details->id)])}}" class="title-one mb-0">{{@ucfirst($product_title)?: ''}}</a></h6>
                                    <h6 class="quantity">Volume <span>: {{@$result->product_variants->variant_size? $result->product_variants->variant_size.' '.$product_unit :''}}</span></h6>
                                    <ul>
                                        <li class="product-pricing">
                                            <h5>{{@$result->product_variants->variant_discounted_price? $result->product_variants->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}<span>{{@$result->product_variants->variant_price? $result->product_variants->variant_price.Helper::Settings( 'currency_symbol') :''}}</span></h5>
                                        </li>                                
                                        <li>
                                            <span class="counter mb-0">
                                                <input class="counter__input" type="text" value="{{$result->quantity}}" name="counter" size="5" readonly="readonly"/>
                                                <a class="counter__increment" href="javascript:void(0)">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g id="plus">
                                                            <path id="Vector" d="M19.1999 11.2H12.8V4.79995C12.8 4.35845 12.4416 4 11.9999 4C11.5584 4 11.2 4.35845 11.2 4.79995V11.2H4.79995C4.35845 11.2 4 11.5584 4 11.9999C4 12.4416 4.35845 12.8 4.79995 12.8H11.2V19.1999C11.2 19.6416 11.5584 20 11.9999 20C12.4416 20 12.8 19.6416 12.8 19.1999V12.8H19.1999C19.6416 12.8 20 12.4416 20 11.9999C20 11.5584 19.6416 11.2 19.1999 11.2Z" fill="#242424"/>
                                                        </g> 
                                                    </svg>
                                                </a>
                                                <a class="counter__decrement" href="javascript:void(0)">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M4.95996 12.9203H19.0399C19.5702 12.9203 20 12.4905 20 11.9601C20 11.4298 19.5703 11 19.0399 11H4.95996C4.4298 11.0001 4 11.4299 4 11.9602C4 12.4905 4.4298 12.9203 4.95996 12.9203Z" fill="#242424"/>
                                                    </svg>
                                                </a>
                                            </span>
                                        </li>
                                        <li class="border-top-0">
                                            <a href="javascript:void(0)" onclick="removeCartItem('{{$result->id}}')" class="link-button">Remove</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        @endforeach                        
                    </ul>

                    @else
                    <h3 class="text-center mb-30 text-danger">No data found</h3>
                    @endif


                </div>
            </div>
            <div class="col-md-4">
                <div class="cart-price-block">
                    <table class="grey-card price-details">
                        <thead>
                            <tr>
                                <th class="heading-six" colspan="2">Price Detail ( 2 items)</th>                            
                            </tr>
                        </thead>
                        <tbody>
                            <tr>                            
                                <td>Total Price</td>
                                <td>336 GH₵</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td>&#8722;200 GH₵</td>
                            </tr>
                            <tr>
                                <td>Tax (8%)</td>
                                <td>30GH₵</td>
                            </tr>
                            <tr class="convenience-row toggle">
                                <td>Convenience
                                    <span>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.2981 4.99086L8.00022 10.29L2.70234 4.99022C2.44975 4.73763 2.04116 4.73763 1.78857 4.99022C1.53662 5.24281 1.53662 5.65204 1.78857 5.90462L7.54305 11.661C7.79499 11.9136 8.20423 11.9136 8.45617 11.661L14.2106 5.90468C14.4626 5.65209 14.4626 5.24222 14.2106 4.98964C13.9593 4.73827 13.5501 4.73827 13.2981 4.99086Z" fill="#858584"/>
                                        </svg>
                                    </span>
                                </td>
                                <td>8 GH₵</td>
                            </tr>
                            <tr class="convenience-inner">
                                <td colspan="2" class="pb-0">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td>Delivery Fee</td>
                                                <td>6 GH₵</td>
                                            </tr>
                                            <tr>
                                                <td>Fulfilment Fee</td>
                                                <td>2 GH₵</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-0" colspan="2"><hr class="m-0"></td>
                            </tr>
                            <tr class="total-amount">
                                <td class="heading-five">Total Amount</td>
                                <td class="heading-five">374 GH₵</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="checkout.php" class="solid-button w-100">place order</a>
                                </td>
                            </tr>                                   
                        </tbody>
                    </table>
                </div>                            
            </div>
        </div>                
    </div>
</section>
<!-- Cart End -->
<!-- Recently Viewed -->
<section class="best-seller py-60">
    <div class="container">
        <h2 class="mb-30">Recently Viewed</h2>            

        <div class="best-seller-wrapper">
            <div class="swiper best-seller-slider recently-viewed-slider pb-30">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="bs-box">
                            <div class="bs-image">
                                <img src="images/Rose-Sanglovese.png" alt="Rose-Sanglovese" title="Rose-Sanglovese" />
                            </div>
                            <div class="bs-content">
                                <h6><a href="product-detail.php" class="heading-six"></a>Rose of Sanglovese 2018</h6>
                                <span class="text-sm grey-text">6-Pack 250 ML</span>
                                <div class="price-wrapper">
                                    <span class="sell-price">168 GH₵</span>
                                    <span class="original-price">268 GH₵</span>
                                </div>
                                <div class="product-rating">
                                    <span class="text-sm black-text">4.2</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                <a href="product-detail.php" class="add-bucket"><i class="icon-bucket"></i></a>
                                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
                            </div>
                            <input class="fav-icon checked_box" type="checkbox" id="fav-item{{$key2}}" value="{{ ($fav_data != "") ? '1' : "0" }}" {{ ($fav_data != "") ? 'checked' : "" }} onclick="return productFav({{$result->id}},{{ ($fav_data != "") ? '1' : "0" }})" />
                            <label class="fav-button" for="fav-item{{$key2}}"></label>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="bs-box">
                            <div class="bs-image">
                                <img src="images/Jack-Daniels.png" alt="Jack-Daniel" title="Jack-Daniel" />
                            </div>
                            <div class="bs-content">
                                <h6><a href="product-detail.php" class="heading-six"></a>Jack Daniels old no.7..</h6>
                                <span class="text-sm grey-text">6-Pack 250 ML</span>
                                <div class="price-wrapper">
                                    <span class="sell-price">168 GH₵</span>
                                    <span class="original-price">268 GH₵</span>
                                </div>
                                <div class="product-rating">
                                    <span class="text-sm black-text">4.2</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                <a href="product-detail.php" class="add-bucket"><i class="icon-bucket"></i></a>
                                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
                            </div>
                            <input class="fav-icon checked_box" type="checkbox" id="fav-item{{$key2}}" value="{{ ($fav_data != "") ? '1' : "0" }}" {{ ($fav_data != "") ? 'checked' : "" }} onclick="return productFav({{$result->id}},{{ ($fav_data != "") ? '1' : "0" }})" />
                            <label class="fav-button" for="fav-item{{$key2}}"></label>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="bs-box">
                            <div class="bs-image">
                                <img src="images/Pedroncelli.png" alt="Pedroncelli" title="Pedroncelli" />
                            </div>
                            <div class="bs-content">
                                <h6><a href="product-detail.php" class="heading-six"></a>Pedroncelli-90 Wisdom</h6>
                                <span class="text-sm grey-text">6-Pack 250 ML</span>
                                <div class="price-wrapper">
                                    <span class="sell-price">168 GH₵</span>
                                    <span class="original-price">268 GH₵</span>
                                </div>
                                <div class="product-rating">
                                    <span class="text-sm black-text">4.2</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                <a href="product-detail.php" class="add-bucket"><i class="icon-bucket"></i></a>
                                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
                            </div>
                            <input class="fav-icon checked_box" type="checkbox" id="fav-item{{$key2}}" value="{{ ($fav_data != "") ? '1' : "0" }}" {{ ($fav_data != "") ? 'checked' : "" }} onclick="return productFav({{$result->id}},{{ ($fav_data != "") ? '1' : "0" }})" />
                            <label class="fav-button" for="fav-item{{$key2}}"></label>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="bs-box">
                            <div class="bs-image">
                                <img src="images/Woodford-Reserve.png" alt="Woodford-Reserve" title="Woodford-Reserve" />
                            </div>
                            <div class="bs-content">
                                <h6><a href="product-detail.php" class="heading-six"></a>Woodford Reserve</h6>
                                <span class="text-sm grey-text">6-Pack 250 ML</span>
                                <div class="price-wrapper">
                                    <span class="sell-price">168 GH₵</span>
                                    <span class="original-price">268 GH₵</span>
                                </div>
                                <div class="product-rating">
                                    <span class="text-sm black-text">4.2</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                <a href="product-detail.php" class="add-bucket"><i class="icon-bucket"></i></a>
                                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
                            </div>
                            <input class="fav-icon" type="checkbox" id="fav-item4"/>
                            <label class="fav-button" for="fav-item4"></label>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="bs-box">
                            <div class="bs-image">
                                <img src="images/Pedroncelli.png" alt="Pedroncelli" title="Pedroncelli" />
                            </div>
                            <div class="bs-content">
                                <h6><a href="product-detail.php" class="heading-six"></a>Pedroncelli-90 Wisdom</h6>
                                <span class="text-sm grey-text">6-Pack 250 ML</span>
                                <div class="price-wrapper">
                                    <span class="sell-price">168 GH₵</span>
                                    <span class="original-price">268 GH₵</span>
                                </div>
                                <div class="product-rating">
                                    <span class="text-sm black-text">4.2</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                <a href="product-detail.php" class="add-bucket"><i class="icon-bucket"></i></a>
                                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
                            </div>
                            <input class="fav-icon" type="checkbox" id="fav-item5"/>
                            <label class="fav-button" for="fav-item5"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-btn-wrapper">
                <div class="recently-viewed-button-prev common-btn-prev"></div>
                <div class="recently-viewed-button-next common-btn-next"></div>
            </div>
            <div class="swiper-scrollbar best-seller-scrollbar recently-viewed-scrollbar common-scroll"></div>
        </div>
    </div>
</section>
<!-- End Recently Viewed -->
<!-- Top Selling Products -->
<section class="best-seller py-60">
    <div class="container">
        <h2 class="mb-30">Top Selling Products</h2>            

        <div class="best-seller-wrapper">
            @if(isset($top_products)  && count($top_products) > 0)
            <div class="swiper best-seller-slider top-selling-slider pb-30">
                <div class="swiper-wrapper">
                    @foreach ($top_products as $result )
                    @php
                        $product_title='';
                        if(session::get('language')==1){
                            $product_title = $result->product_name;
                            $image_not_found = 'Image not available';
                        }else{
                            $product_title = ($result->product_name_fr)?$result->product_name_fr:$result->product_name;
                            $image_not_found = 'Image non disponible';
                        }   
                        $product_image = $result->get_product_images->first();
                        $product_variant = $result->get_product_variants->first();              
                        $product_unit = Helper::getUnitById($product_variant->variant_uof);           
                    @endphp
                    <div class="swiper-slide">
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
                                    <span class="sell-price">{{@$product_variant->variant_discounted_price? $product_variant->variant_discounted_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                    <span class="original-price"> {{@$product_variant->variant_price? $product_variant->variant_price.Helper::Settings( 'currency_symbol') :''}}</span>
                                </div>
                                @if(!empty($result->average_rating))
                                <div class="product-rating">
                                    <span class="text-sm black-text">{{@$result->average_rating?: ''}}</span>
                                    <i class="icon-star-fill"></i>
                                </div>
                                @endif
                                <a href="product-detail.php" class="add-bucket"><i class="icon-bucket"></i></a>
                                <!-- <a href="#" class="solid-button add-to-cart"><i class="icon-cart"></i>add to bucket</a> -->
                            </div>
                            <input class="fav-icon" type="checkbox" id="fav-item1"/>
                            <label class="fav-button" for="fav-item1"></label>
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
<!-- End Top Selling Products -->

@endsection
@push('after-scripts')
    <script>
        function removeCartItem(cid){
            $.ajax({
                type:"post",               
                data:{cart_id:cid},
                url: "{{route('cartremove')}}",
                success: function(data) {  
                    location.reload();
                }
            });
        }
    </script>
@endpush