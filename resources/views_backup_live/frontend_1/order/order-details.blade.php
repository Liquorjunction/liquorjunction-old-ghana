@extends('frontend.layouts.app')
@section('title', 'Order Detail')
@section('content')
    @include('sweetalert::alert')
    <style>
        .storename {
            color: gray;
        }
    </style>
    <div class="bread-crumb-block">
        <div class="container">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">{{ @Helper::language('home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ url('my-order') }}">{{ Helper::language('my_order') }} </a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Helper::language('order_detail') }}</li>
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
                    <h2>{{ Helper::language('order_detail') }}</h2>
                    <div class="row">
                        <div class="col-12">
                            <div class="common-card order-list order-details">
                                <ul class="order-detail">
                                    <li>
                                        <span
                                            class="body-normal text-dark-grey d-block mb-1">{{ Helper::language('order_placed') }}</span>
                                        <span class="body-normal text-dark-grey d-block mb-0">
                                            @php
                                                $order_placed = \Helper::converttimeTozone(
                                                    $orderInformation->created_at,
                                                );
                                                echo $order_palce_date = date('d M Y', strtotime($order_placed));
                                            @endphp
                                        </span>
                                    </li>
                                    @if ($orderInformation->order_type == 1)
                                        <li>
                                            <span
                                                class="body-normal text-dark-grey d-block mb-1">{{ Helper::language('deliver_to') }}</span>
                                            <span class="body-normal text-dark-grey d-block mb-0">
                                                @php

                                                    if (strpos($orderInformation->delivery_address, ',|') !== false) {
                                                        $order_customer_name = explode(
                                                            ',|',
                                                            $orderInformation->delivery_address,
                                                        );
                                                        echo $order_customer_name = $order_customer_name[0];
                                                    } else {
                                                        $order_customer_name = explode(
                                                            ', ',
                                                            $orderInformation->delivery_address,
                                                        );
                                                        echo $order_customer_name = $order_customer_name[0];
                                                    }
                                                @endphp
                                            </span>
                                        </li>
                                    @endif
                                    {{-- @if ($orderInformation->order_type == 2)
                                <li>
                                    <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('pickup_order')}}</span>
                                    <span class="body-normal text-dark-grey d-block mb-0">
                                        @php
                                            
                                                $pickup  = explode(',| ', $orderInformation->orderInfo->store_pickup_address);
                                                echo $pickup[0];
                                        @endphp        
                                    </span>
                                </li>
                                @endif --}}
                                    <li>
                                        <span
                                            class="body-normal text-dark-grey d-block mb-1">{{ Helper::language('order_type') }}</span>
                                        <span class="body-normal text-dark-grey d-block mb-0">
                                            @if ($orderInformation->order_type == 1)
                                                {{ 'Online' }}
                                            @else
                                                {{ 'Pickup Order' }}
                                            @endif
                                        </span>
                                    </li>

                                    <li class="flex-md-fill">
                                        <span
                                            class="body-normal text-dark-grey d-block mb-1">{{ Helper::language('order_status') }}</span>
                                        <span
                                            class="body-normal text-dark-grey d-block mb-0">{{ Helper::getOrderStatus($orderInformation->order_status) }}</span>
                                    </li>
                                    {{-- <li>
                                    <span class="body-normal text-dark-grey d-block mb-1">Note</span>
                                    <span class="body-normal text-dark-grey d-block mb-0">
                                       {{@$orderData->note}}
                                    </span>
                                </li>                          --}}
                                    <li>
                                        <span
                                            class="body-normal text-dark-grey d-block mb-1">{{ Helper::language('order') }}
                                            #{{ @$orderInformation->order_id }}</span>
                                        <span class="body-normal text-black text-bold d-block mb-0">
                                            {{ Helper::language('payment_method_label') }}:<span
                                                class="body-normal text-black text-bold d-inline-block ms-1">
                                                @php
                                                    if (@$orderInformation->transcations->payment_type == 1) {
                                                        echo 'Card(Debit/Credit)';
                                                    } elseif (@$orderInformation->transcations->payment_type == 2) {
                                                        echo 'Cart';
                                                    } elseif (@$orderInformation->transcations->payment_type == 3) {
                                                        echo 'Cash On Delivery';
                                                    } else {
                                                        echo '-';
                                                    }
                                                @endphp
                                            </span></span>
                                    </li>
                                </ul>
                                <div class="order-details-info">
                                    <div class="row">
                                        <div class="col-lg-8 col-sm-6 address-column">
                                            <div class="common-card account-address">
                                                @if ($orderInformation->order_type == 1)
                                                    <h5>{{ Helper::language('delivery_address') }}</h5>
                                                    <?php
                                                    if ( strpos($orderInformation->delivery_address, ',|' ) !== false ) {
                                                        $order_delivery_address  = explode(',| ', $orderInformation->delivery_address);   
                                                         //removing first element from array, it means name.         
                                                        $name = array_shift($order_delivery_address);    
                                                        //removing last element from array, it means mobile number.                                                         
                                                        $number = array_pop($order_delivery_address);                                                     
                                                ?>
                                                    <p class="title-two">{{ @$name ?: '' }}</p>
                                                    <address class="body-large title-two">
                                                        <?php
                                                        echo implode(', ', $order_delivery_address);
                                                        ?>
                                                    </address>
                                                    <a tel="{{ @$number ?: '' }}"
                                                        class="body-large">{{ @$number ?: '' }}</a>
                                                    <?php
                                                    }
                                                ?>
                                                @elseif($orderInformation->order_type == 2)
                                                    <h5>{{ Helper::language('pickup_order') }}</h5>
                                                    <?php
                                                    if ( strpos($orderInformation->orderInfo->store_pickup_address, ',|' ) !== false ) {
                                                        $order_pickup_address  = explode(',| ', $orderInformation->orderInfo->store_pickup_address);   
                                                         //removing first element from array, it means name.         
                                                        $name = array_shift($order_pickup_address);    
                                                        $storenumber = array_pop($order_pickup_address);    
                                                ?>
                                                    <p class="title-two">{{ @ucfirst($name) ?: '' }}</p>
                                                    <address class="body-large title-two">
                                                        <?php
                                                        echo implode('', $order_pickup_address);
                                                        ?>
                                                    </address>
                                                    <a tel="{{ @$storenumber ?: '' }}"
                                                        class="body-large">{{ @$storenumber ?: '' }}</a>
                                                    <?php
                                                    }
                                                ?>
                                                    @php
                                                        // echo $orderInformation->orderInfo->store_pickup_address;
                                                        // $order_customer_name  = explode(', ', $orderInformation->orderInfo->store_pickup_address);
                                                        ///$firstWord = '<p class="storename">'.trim($order_customer_name[0]).'</p>';

                                                        // $pickup  = $firstWord.' '.$order_customer_name[1].',<br> '.$order_customer_name[2].',<br> '.$order_customer_name[3].',<br> '.$order_customer_name[4].',<br> '.$order_customer_name[5].',<br> '.$order_customer_name[6];

                                                        // echo $pickup;
                                                    @endphp
                                                @else
                                                    -
                                                @endif
                                                {{-- <p class="title-two">William Costello</p>
                                            <address class="body-large title-two">Great Westerford Building, 240, Main Road, Rondebosch, Cape Town</address>
                                            <a href="tel:+2740126651" class="body-large">+27 4012 6651</a>                                                  --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6 col-12">
                                            @php
                                                // if($orderInformation->order_details){
                                                //     $orignal_price = 0;
                                                //     $selling_price = 0;
                                                //     foreach ($orderInformation->order_details as $key => $order_detail) {
                                                //         $orignal_price += $order_detail->product_original_amount;
                                                //         $selling_price += $order_detail->product_total_amount;
                                                //     }
                                                // }
                                            @endphp
                                            <ul class="order-summary-list mb-0">
                                                <li>
                                                    <span
                                                        class="heading-six mb-0">{{ Helper::language('price_detail_label') }}({{ @count($orderInformation->order_details) ?: '' }}
                                                        {{ Helper::language('items') }})</span>
                                                </li>
                                                <li>
                                                    <span
                                                        class="body-large text-dark-grey">{{ Helper::language('total_mrp') }}</span>
                                                    <span class="body-large text-dark-grey">
                                                        @php
                                                            $product_original_amount = 0;
                                                            $product_discount_amount = 0;
                                                            $qty = 0;
                                                            if ($orderInformation->order_details) {
                                                                foreach ($orderInformation->order_details as $result) {
                                                                    $product_original_amount +=
                                                                        $result->product_original_amount *
                                                                        $result->quantity;
                                                                    if ($result->product_total_amount != 0) {
                                                                        $disc_price =
                                                                            $result->product_original_amount -
                                                                            $result->product_total_amount;
                                                                        $product_discount_amount +=
                                                                            $disc_price * $result->quantity;
                                                                    }
                                                                };
                                                            }
                                                            echo @Helper::numberFormat(
                                                                $variant_original_price = $product_original_amount,
                                                            ) .
                                                                ' ' .
                                                                Helper::Settings('currency_symbol');
                                                            // $variant_original_price =($orderInformation->order_details->sum('product_original_amount') * $orderInformation->order_details->sum('quantity'));

                                                            // echo $variant_original_price.Helper::Settings( 'currency_symbol');
                                                            //    echo $discount_price = $product_discount_amount;
                                                            //    if($product_discount_amount!=0){

                                                            //       //echo  $discount_price =  ($product_original_amount-$product_discount_amount) ;
                                                            //    }
                                                        @endphp
                                                    </span>
                                                </li>
                                                @if ($product_discount_amount != 0)
                                                    <li>
                                                        <span
                                                            class="body-large text-dark-grey">{{ Helper::language('discount_label') }}</span>
                                                        <span class="body-large text-green"> -
                                                            {{ @Helper::numberFormat($product_discount_amount) . ' ' . Helper::Settings('currency_symbol') }}
                                                        </span>
                                                    </li>
                                                @endif
                                                @if ($orderInformation->tax)
                                                    <li>
                                                        <span class="body-large text-dark-grey">Tax</span>
                                                        <span
                                                            class="body-large text-dark-grey">{{ @Helper::numberFormat($orderInformation->tax) . ' ' . Helper::Settings('currency_symbol') }}</span>
                                                    </li>
                                                @endif
                                                @if (isset($orderInformation->orderInfo->delivery_fee) && $orderInformation->orderInfo->delivery_fee != 0)
                                                    <li>
                                                        <span
                                                            class="body-large text-dark-grey">{{ Helper::language('delivery_fee_lab') }}</span>
                                                        <span class="body-large text-dark-grey">
                                                            {{ @Helper::numberFormat($orderInformation->orderInfo->delivery_fee) . ' ' . Helper::Settings('currency_symbol') }}</span>
                                                    </li>
                                                @endif
                                                {{-- Below is coupon amount --}}
                                                @if (isset($orderInformation->discount_amount) && $orderInformation->discount_amount != 0)
                                                    <li>
                                                        <span
                                                            class="body-large text-dark-grey">{{ Helper::language('coupon_discount') }}</span>
                                                        <span class="body-large text-green">-
                                                            {{ @Helper::numberFormat($orderInformation->discount_amount) . ' ' . Helper::Settings('currency_symbol') }}</span>
                                                    </li>
                                                @endif
                                                <li>
                                                    <span
                                                        class="heading-five mb-0">{{ Helper::language('total_amount_label') }}</span>
                                                    <span
                                                        class="heading-five mb-0">{{ @Helper::numberFormat($orderInformation->payable_amount) . ' ' . Helper::Settings('currency_symbol') }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    @if($orderData->note)
                                    <p style="font-size: 18px;"><b>Note</b> :- {{$orderData->note}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 mt-30">
                            <ul class="common-card recent-order order-list order-detail-list">
                                <li>
                                    <div class="order-detail-heading">
                                        <h5 class="mb-0">
                                            {{ Helper::language('total_Item') }}({{ @count($orderInformation->order_details) ?: '' }})
                                        </h5>
                                        {{-- <h6 class="mb-0">{{Helper::language('delivery_on')}} <span class="d-inline-block text-yellow">20 January 2023</span></h6> --}}
                                    </div>
                                    {{-- <p class="mb-0"> {{Helper::language('sold_by')}} : <a href="#" class="text-grey">Liquor Boulevard</a></p>
                                <p class="samll-text">2425 Doreen St, Swartklip, Limpopo, South Africa, 0375</p> --}}
                                </li>
                                <li>
                                    <ul class="order-listing">
                                        @if ($orderInformation->order_details)
                                            @foreach ($orderInformation->order_details as $result)
                                                @php
                                                    $unit = @Helper::getUnitById(@$result->variant_unit);
                                                    $product_info = Helper::getProductDetails(@$result->product_id);
                                                    $product_image = $product_info->get_product_images->first();
                                                    $product_title = '';
                                                    if (session::get('language') == 2) {
                                                        $product_title = $product_info->product_name_fr;
                                                    } else {
                                                        $product_title = $product_info->product_name;
                                                    }
                                                @endphp
                                                <li>
                                                    <div class="single-product">
                                                        <div class="product-img">
                                                            <a
                                                                href="{{ route('productdetails', ['id' => Helper::encodeUrl(@$result->product_id)]) }}">
                                                                @if (file_exists(public_path() . '/uploads/product/' . $product_image->image))
                                                                    <img src="{{ asset('uploads/product/' . $product_image->image) }}"
                                                                        title="{{ $product_title }}" />
                                                                @else
                                                                    <img src="{{ asset('assets/frontend/images/image-not-avilable.png') }}"
                                                                        title="{{ Helper::language('image_not_available') }}"
                                                                        alt="{{ Helper::language('image_not_available') }}">
                                                                @endif
                                                            </a>
                                                        </div>
                                                        <div class="product-detail">
                                                            <div class="product-detail-main">
                                                                <h6><a href="{{ route('productdetails', ['id' => Helper::encodeUrl(@$result->product_id)]) }}"
                                                                        class="heading-six mb-0">{{ ucfirst($product_title) }}</a>
                                                                </h6>
                                                                <p class="quantity"> {{ Helper::language('volume') }}
                                                                    <span>: {{ @$result->variant_size . ' ' . $unit }}</span>
                                                                </p>
                                                                <p class="quantity">
                                                                    {{ Helper::language('quantity') }}<span>:
                                                                        {{ @$result->quantity }}</span></p>
                                                            </div>
                                                            <ul>
                                                                @if (@$result->product_total_amount != 0)
                                                                    <li class="product-pricing">
                                                                        <h5>{{ @$result->product_total_amount . Helper::Settings('currency_symbol') }}<span>{{ $result->product_original_amount . Helper::Settings('currency_symbol') }}</span>
                                                                        </h5>
                                                                    </li>
                                                                @else
                                                                    <li class="product-pricing">
                                                                        <h5>{{ @$result->product_original_amount . Helper::Settings('currency_symbol') }}
                                                                        </h5>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                        <?php
                                                        $user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
                                                        ?>
                                                        @if ($orderInformation->order_status == 3)
                                                            @php
                                                                $is_user_reviwed = Helper::getUserProductOrderReview(
                                                                    @$result->product_id,
                                                                    @$orderInformation->id,
                                                                );
                                                            @endphp
                                                            @if ($is_user_reviwed == 0)
                                                                <div class="review_btn">
                                                                    <button class="solid-button"
                                                                        onclick="getProductId('{{ $result->id }}','{{ @$orderData->id }}','{{ $product_info->id }}')"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#OrderSuccessfully"
                                                                        id="addReview">{{ Helper::language('add_review') }}</button>
                                                                </div>
                                                            @endif
                                                        @endif
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                            </ul>
                            @if ($orderInformation->order_status != 3 && $orderInformation->order_status != 4)
                                <div class="common-card add-review-block">
                                    {{-- <h5 class="mb-3">Cancel your order</h5> --}}
                                    <a class="solid-button" onclick='cancellOrder("{{ $orderInformation->uniqid }}")'
                                        href="javascript::void(0)">Cancel order</a>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-5 mt-30">

                            <!-- <div class="common-card product-tracking">
                                <h5 class="mb-1">Tracking Order</h5>
                                <span class="body-normal text-dark-grey">Tracking ID  #12345ABC</span>
                                <ul class="product-tracking-block">
                                    <li>
                                        <span class="body-normal text-black text-bold">Monday, 30 January</span>
                                    </li>
                                    <li>
                                        <span class="body-normal text-dark-grey">6:48 PM</span>
                                        <div class="tracking-info">
                                            <span class="body-normal text-dark-grey d-block">Delivered IN</span>
                                            <label class="text-dark-grey">South Australia</label>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="body-normal text-dark-grey">8:28 AM</span>
                                        <div class="tracking-info">
                                            <span class="body-normal text-dark-grey d-block">Out of delivery</span>
                                            <label class="text-dark-grey">Australia</label>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="body-normal text-dark-grey">8:08 AM</span>
                                        <div class="tracking-info">
                                            <span class="body-normal text-dark-grey d-block">Package arrived at delivery station</span>
                                            <label class="text-dark-grey">Australia</label>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="body-normal text-black text-bold">Sunday, 29 January</span>
                                    </li>
                                    <li>
                                        <span class="body-normal text-dark-grey">9:48 PM</span>
                                        <div class="tracking-info">
                                            <span class="body-normal text-dark-grey d-block">Package arrived at trade25 station</span>
                                            <label class="text-dark-grey">South Australia</label>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="body-normal text-dark-grey">9:48 PM</span>
                                        <div class="tracking-info">
                                            <span class="body-normal text-dark-grey d-block">Package arrived at trade25 station</span>
                                            <label class="text-dark-grey">South Australia</label>
                                        </div>
                                    </li>
                                </ul>
                            </div> -->
                            <!-- <div class="common-card add-review-block">
                                <h5 class="mb-1">Write Your Review</h5>
                                <p class="text-sm">Share  your own experience about product and service.</p>
                                <button class="solid-button" data-bs-toggle="modal" data-bs-target="#OrderSuccessfully">Add Review</button>
                            </div> -->

                            <?php
                            $user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
                            $ratingData = DB::table('ratings')
                                ->leftjoin('main_users', 'main_users.id', '=', 'ratings.user_id')
                                ->leftjoin('product', 'product.id', '=', 'ratings.product_id')
                                ->leftjoin('order', 'order.id', '=', 'ratings.order_id')
                                ->where('ratings.status', 1)
                                ->select('ratings.*', 'main_users.first_name', 'main_users.last_name')
                                ->where('ratings.user_id', $user_id)
                                ->where('ratings.order_id', $orderInformation->id)
                                ->get();
                            ?>
                            @if (isset($ratingData) && count($ratingData) > 0)
                                <div class="common-card your-review-block">
                                    <h5 class="mb-3">{{ Helper::language('your_review') }}</h5>
                                    <ul class="mb-0">
                                        <li>
                                            @foreach ($ratingData as $rating)
                                                <div class="your-review-box">
                                                    <span
                                                        class="your-review-img">{{ ucfirst(substr($rating->first_name, 0, 1)) }}
                                                        {{ ucfirst(substr($rating->last_name, 0, 1)) }}</span>
                                                    <div class="review-block mb-0">
                                                        <div class="review-star-rating">
                                                            @for ($i = 1; $i <= round($rating->ratings); $i++)
                                                                <i class="icon-star-fill"></i>
                                                            @endfor
                                                        </div>
                                                        <span class="text-sm">{{ ucfirst($rating->first_name) }}
                                                            {{ $rating->last_name }}</span>
                                                        <p>{{ $rating->review }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Add Review -->
    <div class="modal order-successfully-modal add-review-modal fade p-0" id="OrderSuccessfully" tabindex="-1"
        aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id ="closeBtn">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g id="icon_cross">
                                <path id="Icon" d="M24 8L8 24M8 8L24 24" stroke="#242424" stroke-width="2"
                                    stroke-linecap="square" stroke-linejoin="round" />
                            </g>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>{{ Helper::language('add_review') }}</h4>
                    <form name="reviewform" id="reviewForm">
                        <!-- <div class="star-rating">
                            <input id="star-1" type="checkbox" name="rating" class="star-icon" value="1" />
                            <label for="star-1" title="1 star" class="star-button"></label>
                            <input id="star-2" type="checkbox" name="rating" class="star-icon" value="2"/>
                            <label for="star-2" title="2 stars" class="star-button"></label>
                            <input id="star-3" type="checkbox" name="rating" class="star-icon" value="3" />
                            <label for="star-3" title="3 stars" class="star-button"></label>
                            <input id="star-4" type="checkbox" name="rating" class="star-icon" value="4"/>
                            <label for="star-4" title="4 stars" class="star-button"></label>
                            <input id="star-5" type="checkbox" name="rating" class="star-icon" value="5" />
                            <label for="star-5" title="5 stars" class="star-button"></label>
                            
                        </div> -->
                        <input type="hidden" id="user_rating" name="rating" value="" />
                        <div class="stars-box">
                            <i class="star rating-star" title="1 star" data-message="Poor" data-value="1"></i>
                            <i class="star rating-star" title="2 stars" data-message="Too bad" data-value="2"></i>
                            <i class="star rating-star" title="3 stars" data-message="Average quality"
                                data-value="3"></i>
                            <i class="star rating-star" title="4 stars" data-message="Nice" data-value="4"></i>
                            <i class="star rating-star" title="5 stars" data-message="very good qality"
                                data-value="5"></i>
                        </div>
                        <p><span id="rating-error" style="color:#FF4444;"></span></p>
                        <div class="form-group">
                            <label for="">{{ Helper::language('comment') }}</label>
                            <textarea name="review" id="review" col="5" rows="2"
                                placeholder="{{ Helper::language('enter_comment') }}"></textarea>
                            <span id="review-error" style="color:#FF4444;float:left;"></span>
                        </div>
                        <button class="solid-button w-100 mt-2">{{ Helper::language('submit_btn') }}</button>
                        <input type="hidden" name="orderId" id="orderId" value="">
                        <input type="hidden" name="productId" id="product_id" value="">
                        <input type="hidden" name="userId" id="user_id" value="">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Review End -->



@endsection

@push('after-scripts')
    <script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>
    <script>
        $(document).ready(function() {
            // Your modal close button click event
            $('#closeBtn').on('click', function() {
                $('#reviewForm')[0].reset();

                $("span#rating-error").text('');
                $("span#review-error").text('');

            });
        });
    </script>
    <script>
        var validation_email_required = "{{ \Helper::language('email_field_required') }}";
        var validation_email = "{{ \Helper::language('enter_valid_email_validation') }}";


        //     $('#myModal').on('hidden.bs.modal', function() {
        //                 areaform.resetForm();
        //                 $('#myModal form')[0].reset();
        //             });

        // $('#addReview').click(function(){
        //     $('.review_btn').hide();
        // });
        function getProductId(product_id, order_id, productId, userId) {
            $("#productId").val(product_id);
            $("#orderId").val(order_id);
            $("#product_id").val(productId)
            $("#userId").val(product_id);
        }

        $(document).ready(function() {
            var product_id = ('#product_id').val();
            var order_id = ('#userId').val();
            if (product_id > 0 && userId > 0) {
                $('.review_btn').hide();
            } else {
                $('.review_btn').show();
            }
        });
        $(document).ready(function() {
            $('#closeBtn').click(function() {

                $('#register_form')[0].reset();
                clearFormErrors();
            });
        });

        function clearFormErrors() {
            // Assuming you have a span with class 'error-message' for each error message
            $('.error-message').hide();
        }
        $(document).ready(function() {
            // Handle checkbox click event
            $('.star-icon').on('click', function() {

                // $(this).prop('checked', !$(this).prop('checked'));

                //             $(this).toggleClass('filled');

                // // Set the clicked star and all stars before it to checked
                //             $(this).prevAll('.star-icon').prop('checked', true);
                //             $(this).prevAll('.star-icon').toggleClass('filled', $(this).prop('checked'));

                $(this).prevAll('.star-icon').prop('checked', true);
                // $(this).prop('checked', true);
                $(this).current('.star-icon').prop('checked', true);

                // Uncheck all stars after the clicked one
                $(this).prop('checked', false);
                $(this).nextAll('.star-icon').prop('checked', false);

                // Log the selected rating (you can do something else with it)
                var selectedRating = $('.star-icon').is('checked:').val();
                alert(selectedRating);
                console.log('Selected Rating:', selectedRating);
            });
        });

        // function loadData(){
        //     var orderId = $('#orderId').val();
        //         // alert(orderId);
        //     var productId=$('#productId').val(); 
        //     var formData = $('#reviewForm').val();
        //     // alert(productId);
        //         $.ajax({
        //         url:"{{ route('addRating') }}",
        //         type:'POST',
        //         data:{
        //             orderId:orderId,
        //             productId:productId,
        //             formData : formData,
        //         },
        //         success:function(response){

        //         }

        //     });
        // }

        $(document).ready(function() {
            $('.star').click(function() {
                var rating_star = $(".selected").length;
                $("#user_rating").val(rating_star);
            });
        });

        var test = $("#reviewForm").validate({
            // rules: {
            //     review: {
            //         required: true,
            //     },
            // },
            // // in 'messages' user have to specify message as per rules
            // messages: {

            //     review: {
            //             required: "Comment field is required",
            //     },

            // },  
            submitHandler: function() {
                var form_data = new FormData($('#reviewForm')[0]);


                var action_url = "{{ route('addRating') }}";
                var orderId = $('#orderId').val();
                // alert(orderId);
                var productId = $('#productId').val();
                // alert(productId);
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
                    beforeSend: function() {
                        $(".loader").fadeIn();
                        $('.loader').css("visibility", "visible");
                    },
                    success: function(response) {
                        $('#OrderSuccessfully').modal('hide');
                        Swal.fire({
                            position: "top-center",
                            icon: "success",
                            text: "Add Review successfully",
                            //showConfirmButton: false,
                            timer: 2500
                        });
                        window.location.reload();
                    },

                    error: function(errors) {
                        // $('.loader').css("visibility", "none");
                        $('.loader').css("visibility", "hidden");
                        var errors = errors.responseJSON;
                        $("span#rating-error,span#review-error").text('');
                        if (errors.rating) {
                            $("span#rating-error").text(errors.rating[0]);
                        }
                        if (errors.review) {
                            $("span#review-error").text(errors.review[0]);
                        }

                    }
                });
            }
        });

        function cancellOrder(order_id) {
            var url = "{{ url('cancel-order') }}/" + order_id;
            Swal.fire({
                text: "{{ Helper::language('customer_cancel_order_alert') }}",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
@endpush
