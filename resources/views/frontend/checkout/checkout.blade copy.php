@extends('frontend.layouts.app')
@section('title',Helper::language('checkout_label'))
@section('content')
@include('sweetalert::alert')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<style>
    .star {
        color: red;
    }

    
</style>
<div class="loader" id="loader"></div>
<div class="bread-crumb-block">
    <div class="container">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page"> {{@Helper::language('checkout_label')}}</li>
        </ul>
    </div>
</div>

<section class="checkout pt-20 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>{{@Helper::language('checkout_label')}}</h2>
                <form class="purchase-option">
                    <div class="radio-group">
                        <input id="radioPurchaseOne" class="radioPurchase" type="radio" name="purchase_radio" checked value="1" data-class="online-main" />
                        <label for="radioPurchaseOne">{{@Helper::language('online_label_web')}}</label>
                    </div>
                    <div class="radio-group">
                        <input id="radioPurchaseTwo" value="2" class="radioPurchase" type="radio" name="purchase_radio" data-class="pickup-order-main" />
                        <label for="radioPurchaseTwo">{{@Helper::language('pickup_order')}}</label>
                    </div>
                </form>
            </div>
        </div>
        <?php
        // dd($userData);
        ?>
        <div class="online-main purchase-main">
            <div class="row">
                <div class="col-xl-4 col-md-6 checkout-col contact-information">
                    {{-- <h5 class="checkout-head">1. {{@Helper::language('address_label')}}</h5> --}}
                    <h5 class="checkout-head">1. Shipping Address</h5>
                    <!-- Before Login Form -->
                    <div class="checkout-heading-flex-group">
                        <h5 class="mb-0">{{@Helper::language('contact_details_web')}}</h5>
                        <?php
                        // dd();
                        ?>
                        <!-- <p class="mb-0">Already a member? <a href="javascript:void(0)" class="link-button">Login</a></p> -->
                        <p class="after-login">{{($userData->first_name).' '.($userData->last_name)}} | <a href="mailto:{{@$userData->email}}" class="border-button">{{@$userData->email}}</a></p>

                    </div>

                    <!-- End Before Login Form -->
                    <!-- After Login Form -->
                    @foreach($UserAddressData as $key=>$data)
                    <form class="checkout-form">
                        <div class="address-group radio-group">
                            <div class="radio-btn">
                                <input id="radioAddress{{$data->id}}" {{ ($key == 0) ? 'checked' : "" }} type="radio" value="{{$data->id}}" name="address_radio" class="addressRadioCurrent" />
                                <label onclick="checkDeliveryAddressByArea('{{$data->id}}')" for="radioAddress{{$data->id}}"></label>
                                <div class="detail">
                                    <h5>{{@Helper::language('default_billing_address')}}</h5>
                                    <p class="black-text">
                                        @php
                                        ($data->name)? $address = $data->name : '';
                                        ($data->address)? $address .= ', <br>'.$data->address: '';
                                        ($data->area->title)? $address .= ', <br>'.$data->area->title : '';
                                        ($data->region->title)? $address .= ', <br>'.$data->region->title : '';
                                        ($data->country->name)? $address .= ',<br> '.$data->country->name : '';

                                        $phone_number = '(+'.$data->phonecode.') '.$data->phone;
                                        ($phone_number)?$address .= ',<br> '.$phone_number : '';
                                        echo $address ;
                                        @endphp
                                    </p>
                                    <div class="mt-2">
                                        <input type="hidden" name="checkout_page" id="checkout_page" value="1">
                                        <input type="hidden" name="checkout_address" id="checkout_address" value="1">
                                        <input type="hidden" name="address_id" id="address_id" value="{{@$data->id}}">
                                        <input type="hidden" class="user_address_id" name="user_address_id" value="{{@$data->id}}"> <input type="hidden" name="checkout_page" id="checkout_page" value="1">
                                        <input type="hidden" name="checkout_address" id="checkout_address" value="1">
                                        <input type="hidden" name="address_id" id="address_id" value="{{@$data->id}}">
                                        <input type="hidden" class="user_address_id" name="user_address_id" value="{{@$data->id}}">
                                        <a href="javascript:void(0)" class="EditAddress"><u>Add Delivery Instructions.</u></a>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="checkout_page" id="checkout_page" value="1">
                            <input type="hidden" name="checkout_address" id="checkout_address" value="1">
                            <input type="hidden" name="address_id" id="address_id" value="{{@$data->id}}">
                            <input type="hidden" class="user_address_id" name="user_address_id" value="{{@$data->id}}"> <input type="hidden" name="checkout_page" id="checkout_page" value="1">
                            <input type="hidden" name="checkout_address" id="checkout_address" value="1">
                            <input type="hidden" name="address_id" id="address_id" value="{{@$data->id}}">
                            <input type="hidden" class="user_address_id" name="user_address_id" value="{{@$data->id}}">
                            <a href="javascript:void(0)" class="border-button EditAddress">{{@Helper::language('edit_label')}}</a>
                        </div>
                        @endforeach
                    </form>

                    <form class="checkout-form" id="addressNewForm">
                        <div class="address-group radio-group">
                            <div class="radio-btn">
                                <input id="radioAddressSix" type="radio" name="address_radio" class="addressRadioCurrent" data-class="add-new-address" />
                                <label for="radioAddressSix"></label>
                                <div class="detail">
                                    <p class="black-text add_new_address_div">{{@Helper::language('add_new_address_label')}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="add-new-address addressRadioCurrentShow d-none">
                            <div class="checkout-heading-flex-group">
                                <h5 class="mb-0">{{@Helper::language('add_address')}}</h5>
                            </div>
                            <div class="form-group">
                                <label for="">{{@Helper::language('name_label_web')}} <span class="text-red star">*</span></label>
                                <input type="text" value="{{old('name')}}" placeholder="{{@Helper::language('enter_name_web')}}" name="name" id="name" class="" placeholder="">
                            </div>
                            <div class="form-group has-validation">
                                <label for="">{{@Helper::language('phone_number')}} <span class="text-red star">*</span></label>
                                <div class="input-group phone-number">
                                    <select class="numbers" name="phonecode">
                                        <?php 
                                            $countryData = @$countryData->sortBy(['name', 'ASC']);
                                            $countryData = $countryData->values();
                                        ?>
                                        @foreach($countryData as $value)
                                        <option value="{{$value->phonecode}}">+{{$value->phonecode.' ('.$value->shortname.')' }}</option>
                                        @endforeach
                                    </select>
                                    <input type="tel" value="{{old('phone')}}" placeholder="{{@Helper::language('enter_phone_number_place')}} " maxlength="15" name="phone" id="phone">

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">{{@Helper::language('country_label_web')}} <span class="text-red star">*</span></label>
                                <select value="{{old('country_id')}}" onchange="getSubCatList(this)" name="country_id" id="country_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_country_web')}}</option>
                                    @foreach($countryData as $value)
                                    <?php $selected = ''; ?>
                                    @if ($value->id == old('country_id'))
                                    <?php $selected = 'selected'; ?>
                                    @endif
                                    <option {{ $selected }} value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">{{@Helper::language('region_label_web')}}<span class="text-red star">*</span></label>
                                <select value="{{old('region_id')}}" onchange="getAreaList(this)" name="region_id" id="region_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_region_web')}}</option>
                                    @foreach($region as $value)
                                    <option value="{{$value->id}}">{{$value->title}}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="">{{@Helper::language('area_label_web')}}<span class="text-red star">*</span></label>
                                <select value="{{old('area_id')}}" name="area_id" id="area_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_area_web')}}</option>
                                    @foreach($area as $value)
                                    <option value="{{$value->id}}">{{$value->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
            <label for="">{{@Helper::language('city_label')}}<span class="text-red star">*</span></label>
            <input type="text" value="{{old('city')}}" placeholder="Enter city" name="city" id="city" class="form-control">
        </div>
                            <div class="form-group">
                                <label for="">{{@Helper::language('street_address')}}<span class="text-red star">*</span></label>
                                <textarea cols="5" rows="2" name="address" placeholder="Enter address"></textarea>
                                <div class="invalid-feedback">
                                    {{@Helper::language('checkout_street_address')}}
                                </div>
                            </div>
                
                            <div class="form-group">
                                <label for="">{{@Helper::language('zip_code_label')}}<span class="text-red star">*</span></label>
                                <input type="text" value="{{old('zip_code')}}" placeholder="Enter zip code" name="zip_code" id="zip_code" class="form-control">
                            </div>

                               {{-- Add delivery instruction --}}
                            <div class="form-group" style="border: 1px solid #DDDDDD; margin-bottom: 15px;padding: 15px 20px 0px 20px;">
                                <label for="instruction">Delivery Instructions (Optional)</label>
                                
                                <div class="mt-2 mb-2">
                                    <label for="instruction">Drop off Options&nbsp;&nbsp;</label>
                                    <br>
                                    <div class="radio-group">
                                        <input id="user_drop" class="radioPurchase" type="radio" name="delivery_options" value="1"/>
                                        <label for="user_drop">Hand it to Me</label>
                                    </div>
                                    &nbsp;&nbsp;
                                    <div class="radio-group">
                                        <input id="door_drop" value="2" class="radioPurchase" type="radio" name="delivery_options"/>
                                        <label for="door_drop">Leave it at my Door</label>
                                    </div>
                                </div>
                                <p class="sidebar-item">Please advise us of any special requirements,if applicable</p>
                                <div class="form-group">
                                    <textarea name="instruction" id="instruction" col="2" rows="1" placeholder="E.g. Leave at front door"></textarea>
                                </div>
                            </div>

                            <input type="hidden" name="checkout_address" id="checkout_address" value="0">
                            <input type="hidden" name="checkout_page" id="checkout_page" value="{{@$checkout_page_count}}">
                            <!-- <a href="javascript:void(0)" class="solid-button w-100" id="addNewAddress">Submit</a> -->
                            <button type="submit" class="solid-button w-100">{{@Helper::language('submit_btn')}}</button>

                        </div>
                    </form>
                    <span class="error red-text" id="user_address_error"></span>
                    <!-- End After Login Form -->


                    <!-- Start Edit Form  -->
                    <form class="checkout-form addressEditform d-none" id="addressEditForm">


                    </form>
                    <!-- End Edit Form -->
                </div>
                <div class="col-xl-4 col-md-6 checkout-col store-contact-information" style="display: none;">
                    <h5 class="checkout-head">1. {{@Helper::language('store_label_web')}}</h5>
                    <div class="checkout-heading-flex-group">
                        <h5 class="mb-0">{{@Helper::language('contact_details_web')}}</h5>
                        <?php
                        // dd();
                        ?>
                        <!-- <p class="mb-0">Already a member? <a href="javascript:void(0)" class="link-button">Login</a></p> -->
                        <p class="after-login">{{($userData->first_name).' '.($userData->last_name)}} | <a href="mailto:{{$userData->email}}" class="border-button">{{$userData->email}}</a></p>

                    </div>
                    <div class="checkout-heading-flex-group">
                        <h5 class="mb-0">{{@Helper::language('select_store_web')}}</h5>
                       
                    </div>  
                    <!-- After Login Form -->
                    
                    <form class="checkout-form" >
                        <div class="address-group store_na" style="display: none;" >
                            <p>N/A</p>
                        </div>
                        <div class="location-permission" style="display: none;" >
                            @if($storeMapData->count() > 0)
                            <?php  ?>
                            @foreach($storeMapData as $key => $map) 
                        
                            <div class="address-group radio-group">
                                <div class="radio-btn">
                                    <input id="radioStoreLocation{{$key}}" type="radio" name="store_location_radio" class="addressRadioCurrent" value="{{@$map->id}}" />
                                    <label onclick="getStoreAddress('{{$map->id}}')" for="radioStoreLocation{{$key}}"></label>
                                    <div class="detail">


                                        <p class="title-two black-text mb-0">{{@$map->store_name}}</p>
                                        <address class="title-two black-text mb-0">{{$map->address}}</address>
                                        @php
                                            $date = date('m/d/Y h:i:s a', time());
                                            $weekname = date('l', strtotime($date));                                       
                                            $weekhours = @Helper::weeklist($weekname);
                                            
                                            $storetime =@Helper::storeTime($map->id,$weekhours->id);
                                            
                                            $start_time =date("H:i", strtotime($storetime->start_time));
                                        // $start_time = Helper::toLocalToUtcTime($storetime->start_time);
                                            $end_time = date("H:i", strtotime($storetime->end_time));
                                        @endphp 
                                        @if($start_time!=="00:00" && $end_time!=="00:00")
                                        <span class="title-two grey-text mb-0">{{@Helper::language('business_hours')}} : {{$start_time}} to {{$end_time}} </span>
                                        @endif
                                        @php
                                            $contactNumber = '';
                                            if($map->phone_code){
                                                $contactNumber .= '+'.$map->phone_code.' ';
                                            }
                                            if($map->contact_number){
                                                $contactNumber .= $map->contact_number;
                                            }

                                        @endphp
                                        @if($contactNumber!="")
                                            <p class="d-flex align-item-center title-two grey-text mb-0">Contact number: <a href="tel:{{ @$contactNumber?:''}}">{{ @$contactNumber?:''}}</a></p> 
                                        @endif
                                        <span class="title-two text-green mb-0">{{@Helper::language('In_store_pickup_only_label')}}</span>
                                    </div>
                                </div>
                                <span class="store-pin text-sm black-text mb-0"><i class="icon-location"></i>{{number_format(@$map->distance,2)}} mi</span>
                            </div>
                            @endforeach
                            @endif
                            <input type="hidden" name="store_id" id="store_id" value="">
                        </div>
                        <span class="error red-text" id="store_location_radio_error"></span>  
                                       
                    </form>
                    <!-- End After Login Form -->

                </div>
                <div class="col-xl-4 col-md-6 checkout-col">
                    <h5 class="checkout-head">2. {{@Helper::language('payment_method_label')}}</h5>
                    <form class="checkout-form">
                        <div class="card-payment-group">
                             <div class="payment-group-block border-bottom-0 mb-0 momo-pay">
                                <div class="radio-group">
                                    <input id="momo" type="radio" name="payment-method" value="1" class="radioCard">
                                    <label for="momo">Credit Card/Debit Card/MOMO</label>
                            <i class="icon-momo"></i>
                        </div>
                </div>
                <!-- <div class="payment-group-block border-bottom-0 mb-0 card-pay">
                    <div class="radio-group">
                        <input id="card" type="radio" value="2" name="payment-method" class="radioCard" data-class="card-detail">
                        <label for="card">Card ( Debit/ Credit)</label>
                        <i class="icon-cards"></i>
                    </div>
                    <div class="card-detail radioCardShow">
                        <div class="form-group">
                            <label for="">Name on card</label>
                            <input type="text" placeholder="Enter name" required="">
                            <div class="invalid-feedback">
                                Please enter a name to continue
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Card number</label>
                            <input type="text" placeholder="Enter card" required="">
                            <div class="invalid-feedback">
                                Please enter a card number to continue
                            </div>
                        </div>
                        <div class="checkout-flex-group expiration-date">
                            <div class="form-group zip-code">
                                <label for="">Expiration date</label>
                                <input type="text" placeholder="mm/yy" required="">
                                <div class="invalid-feedback">
                                    Please enter date to continue
                                </div>
                            </div>
                            <div class="form-group city">
                                <label for="">CVV</label>
                                <input type="text" placeholder="CVV" required="">
                                <div class="invalid-feedback">
                                    Please enter a city to continue
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  -->
                <!-- <div class="payment-group-block mb-2">
                    <div class="radio-group mb-0">
                        <input id="CardOnDelivery" type="radio" value="1" name="payment-method" class="radioCard">
                        <label for="CardOnDelivery"> {{@Helper::language('card_debit_credit_label_web')?:'Card(Debit/Credit)'}}</label>
                        <i class="icon-cards"></i>
                    </div>
                </div> -->
                <div class="payment-group-block mb-0">
                    <div class="radio-group mb-0">
                        <input id="CashOnDelivery" type="radio" value="3" name="payment-method" class="radioCard">
                        <label for="CashOnDelivery"> {{@Helper::language('cash_on_delivery_label')}}</label>
                        <i class="icon-wallet"></i>
                    </div>
                </div>
                <span class="error red-text" id="payment_method_error"></span>
            </div>

            </form>
        </div>
        <div class="col-xl-4 col-md-6 checkout-col">
            <h5 class="checkout-head">3. {{@Helper::language('order_summary')}}</h5>
            <div class="order-summary-body">
                <h6 class="toggle1 toggled-on">{{@count($productData)?:'0'}} {{@Helper::language('items_in_cart')}}</h6>
                <div class="cart-item-list toggled-on">
                    @php
                    $original_price =0;
                    $total_discount_price =0;
                    $product_discount_price = 0;
                    $is_product_discount = false;
                    @endphp
                    @foreach ($productData as $result)
                        @php
                        $product_title = '';
                        if (session::get('language') == 1) {
                            $product_title = $result->get_product_details->product_name;
                            $image_not_found = 'Image not available';
                        } else {
                            $product_title = $result->get_product_details->product_name_fr ? $result->get_product_details->product_name_fr : $result->get_product_details->product_name;
                            $image_not_found = 'Image non disponible';
                        }
                        $product_image = $result->get_product_details->get_product_images->first();
                        $product_unit = Helper::getUnitById($result->variant_uof);
                        //dd($variant_quantity);
                        if(empty($variant_quantity)){

                            $org_price = @$result->variant_price ? ($result->variant_price * Helper::getUserCartQuantity($result->id,$user_id)): 0;
                            $discount_price = 0;
                            if($result->variant_discounted_price!='' && $result->variant_discounted_price!=0){
                                $discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * Helper::getUserCartQuantity($result->id,$user_id)) : 0;
                                $is_product_discount = true;
                            }else{
                                $discount_price = $org_price;
                            }
                        }else{
                            $org_price = @$result->variant_price ? ($result->variant_price * $variant_quantity): 0;
                            $discount_price = 0;
                            if($result->variant_discounted_price!='' && $result->variant_discounted_price!=0){
                                $discount_price = @$result->variant_discounted_price ? ($result->variant_discounted_price * $variant_quantity) : 0;
                                $is_product_discount = true;
                            }else{
                                $discount_price = $org_price;
                            }                            
                        }
                        $original_price += $org_price;
                        $product_discount_price += $discount_price ;
                        $total_discount_price += ($org_price - $discount_price);
                    @endphp
                    <div class="single-product">
                        <div class="product-img">
                            <a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->get_product_details->id)]) }}">
                                @if (file_exists(public_path() . '/uploads/product/' . $product_image->image))
                                <img src="{{ asset('uploads/product/' . $product_image->image) }}" title="{{ $product_title }}" alt="{{ $product_title }}" />
                                @else
                                <img src="{{ asset('assets/frontend/images/image-not-avilable.png') }}" title="{{ $image_not_found }}" alt="{{ $image_not_found }}">
                                @endif
                            </a>
                        </div>
                        <div class="product-detail">
                            <div class="product-detail-main">
                                <h6><a href="{{ route('productdetails', ['id' => Helper::encodeUrl($result->get_product_details->id)]) }}" class="heading-six mb-0">{{ $product_title }}</a></h6>
                                <p class="quantity"> {{@Helper::language('volume')}}<span>: {{ @$result->variant_size ? $result->variant_size . ' ' . $product_unit : '' }}</span></p>
                                @php
                                if(empty($variant_quantity)){
                                    $qty = Helper::getUserCartQuantity($result->id,$user_id);
                                }else{
                                    $qty =$variant_quantity;
                                }
                                @endphp
                                <p class="quantity"> {{@Helper::language('quantity')}}: <span>{{$qty}}</span></p>
                            </div>
                            <ul>
                                <li class="product-pricing">
                                    <h5>
                                        @if($result->variant_discounted_price=='' || $result->variant_discounted_price==0)
                                        {{ @$result->variant_price ? $result->variant_price . Helper::Settings('currency_symbol') : '' }}
                                        @else
                                        {{ @$result->variant_discounted_price ? $result->variant_discounted_price . Helper::Settings('currency_symbol') : '' }}
                                        @endif
                                        <span>
                                            @if($result->variant_discounted_price!='' && $result->variant_discounted_price!=0)
                                            {{ @$result->variant_price ? $result->variant_price . Helper::Settings('currency_symbol') : '' }}
                                            @endif
                                        </span>
                                    </h5>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="removeCartItem('{{ $result->id }}')" class="link-button">{{@Helper::language('remove_btn')}}</a>
                                </li>
                                <input type="hidden" value="{{ $result->id }}" name="pvariant_Ids[]">
                                <input type="hidden" value="{{$qty?:0}}" name="buy_quantity[]">
                                <input type="hidden" value="{{@$result->variant_price}}" name="buy_org_price">
                                <input type="hidden" value="{{@$result->variant_discounted_price}}" name="buy_discounted_price">
                            </ul>
                        </div>
                    </div>
                    @endforeach
                    {{-- //loop close/ --}}
                </div>
                <div class="form-group mb-30">
                    <div class="loyalty-points-form-group">
                        <label for=""><i class="icon-discount"></i>{{@Helper::language('coupons')}}</label>
                        <input type="text" id="coupon" value="" required="" placeholder="{{@Helper::language('apply_coupons_plac')}}">
                        <button class="link-button remove-btn" id="apply-coupon">{{@Helper::language('apply')}}</button>
                        <a onclick="location.reload();" style="display: none;" class="link-button remove-btn" id="clear-coupon">{{@Helper::language('clear')}}</a>
                        <span class="" id="coupon_error"></span>
                    </div>
                    <div class="invalid-feedback">
                        Please enter
                    </div>
                </div>
                <div class="form-group mb-30">
                    <label>Does your order contain gift Items? <i class="fas fa-gift" style="width: 20px;height: 20px;"></i> </label>
                    <div class="check-group terms-conditions" style="display: flex; align-items: center;margin:0px !important;">
                        <input class="form-check-input" type="checkbox" value="" id="userGift">
                        <label class="form-check-label"><span style="color: #858584 !important;cursor: default;">Ordering a gift? Check this box to see gift options before checkout.</span></label>
                    </div>
                    <div class="d-none mt-2" id="giftDiv">
                        <label for="">Recipient's Name</label>
                        <input type="text" id="giftRecipient" value="" required="" placeholder="Recipient's Name">
                        <span class="" id="gift_error"></span>
                        <br><br>
                        <label for="">Gift Message (Optional)</label>
                        <textarea cols="5" rows="2" name="giftMessage" id="giftMessage" placeholder="Enter a special message for the recipient here"></textarea>
                    </div>  
                </div>
                <div class="price-detail">
                    <h6> {{@Helper::language('price_detail_label')}}( {{@count($productData)?:'0'}} {{@Helper::language('items')}})</h6>
                    <ul class="price-list">
                        <li><span>{{@Helper::language('total_price')}}</span> <span>{{@Helper::numberFormat($original_price)}} {{Helper::Settings('currency_symbol')}}</span></li>

                        @if($is_product_discount!=false)
                        <li><span>{{@Helper::language('discount_label')}}</span> <span class="text-green">-{{@Helper::numberFormat($total_discount_price)}} {{Helper::Settings('currency_symbol')}}</span></li>
                        @endif

                        @if(Helper::Settings('tax')!=0)
                        <li><span>Tax ( {{Helper::Settings('tax')}} %)</span>
                            @php                          
                            $tax_amount = @Helper::numberFormat(((int) Helper::Settings('tax') / 100) * $product_discount_price);
                            @endphp
                            <span id="tax-amount">{{$tax_amount}} {{Helper::Settings('currency_symbol')}}</span>
                        </li>
                        @php
                        $product_discount_price = @Helper::numberFormat($product_discount_price + $tax_amount);
                        @endphp
                        @endif
                        <li style="display: none;" id="delivery_li"><span id="">{{@Helper::language('delivery_fee_lab')}}</span>
                            <span id="delivery_fee"></span>
                            <input type="hidden" id="inp-delivery-fee" value="">
                        </li>

                        <li id="li-coupon" style="display: none;"><span>{{@Helper::language('copoun_code_amount')}}</span> <span id="coupon_code_amount_text"></span></li>
                        <input type="hidden" value="" id="coupon_code_id">
                        <input type="hidden" value="" id="coupon_percentage">
                        <input type="hidden" value="" name="coupon_code_amount">
                        {{-- <li class="convenience-row toggle">
                                    <span>
                                        Convenience
                                        <span>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M13.2981 4.99086L8.00022 10.29L2.70234 4.99022C2.44975 4.73763 2.04116 4.73763 1.78857 4.99022C1.53662 5.24281 1.53662 5.65204 1.78857 5.90462L7.54305 11.661C7.79499 11.9136 8.20423 11.9136 8.45617 11.661L14.2106 5.90468C14.4626 5.65209 14.4626 5.24222 14.2106 4.98964C13.9593 4.73827 13.5501 4.73827 13.2981 4.99086Z" fill="#858584" />
                                            </svg>
                                        </span>
                                    </span>
                                    <span>8 GH₵</span>
                                </li>
                                <li class="convenience-inner">
                                    <ul>
                                        <li><span>Delivery Fee</span> <span>6 GH₵</span></li>
                                        <li><span>Fulfilment Fee</span> <span>2 GH₵</span></li>
                                    </ul>
                                </li> --}}
                    </ul>
                    <div class="total-amount">
                        <h5>{{@Helper::language('total_amount_label')}}</h5>
                        <h5 id="grand-total-amount">{{@Helper::numberFormat($product_discount_price)}} {{Helper::Settings('currency_symbol')}}</h5>
                        <input type="hidden" id="inp-grand-total-amount" value="">
                    </div>
                </div>
                <div class="check-group terms-conditions">
                    <input class="form-check-input" id="terms_conditions" name="terms_conditions" type="checkbox" value="">
                    <label class="form-check-label">{{@Helper::language('i_agree_to_the')}} <a target="_blank" href="{{route('termsCondition')}}" class="border-button">{{@Helper::language('terms_&_conditions')}}</a>{{@Helper::language('and_label')}} <a href="{{route('privacyPolicy')}}" target="_blank" class="border-button"> {{@Helper::language('privacy_policy')}}</a>
                        <p><span class="error red-text" id="terms_conditions_error"></span></p>
                    </label>
                </div>
                <input type="hidden" value="{{$is_buy_now?:0}}" id="buy-now">

                <a href="javascript:void(0)" class="solid-button w-100" onclick="return placeOrder()">{{@Helper::language('place_order_btn')}}</a>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>
<!--  -->
<div class="modal order-successfully-modal fade p-0" id="OrderSuccessfully" tabindex="-1" aria-modal="true" role="dialog">



</div>
<!--  -->

<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&callback=initMap" async defer></script> -->

<script>
    $('.shippingAddress input[type="checkbox"]').change(function() {
        if ($(this).is(":checked")) {
            $('.shipping-info').addClass('show');
        } else {
            $('.shipping-info').removeClass('show');
        }
    });

    $('.shippingAddress input[type="checkbox"]').change(function() {
        if ($(this).is(":checked")) {
            $('.shipping-info').addClass('show');
        } else {
            $('.shipping-info').removeClass('show');
        }
    });
    // $('.add-new-address input[type="radio"]').change(function() {
    //     alert(1212);

    // });
    $(document).ready(function() {
        $("input[name$='address-radio']").click(function() {
            $('.addressNewform').addClass('d-none');
            $(document).find("#addressEditForm").html('');

        });
    });

    var scrolled = 0;
    $('.EditAddress').click(function() {

        $('#addressEditForm').addClass('d-none');
        $(document).find("#addressNewForm").html('');
        var middleOfPage = $(document).height() / 2;

        // Scroll to the middle of the page with animation
        $('html, body').animate({
            scrollTop: middleOfPage
        }, 1000);

    });
    $('.add_new_address_div').click(function(e) {
        e.preventDefault()
        $('.addressNewform').removeClass('d-none');
        $('.add-new-address').toggleClass('d-none');
        $(document).find("#addressEditForm").html('');
    });


    $('#radioAddressSix').click(function(e) {
        $('.add-new-address').toggleClass('d-none');
    });

    var validation_name_required = "{{ \Helper::language('name_field_is_required'); }}";
    var validation_name_max_required = "{{ \Helper::language('the_name_may_not_be_greater_than_40_characters'); }}";
    var validation_phone_required = "{{ \Helper::language('phone_number_field_is_required'); }}";
    var validation_phone_minlength = "{{ \Helper::language('phone_number_min_max'); }}";
    var validation_phone_maxlength = "{{ \Helper::language('phone_number_min_max'); }}";
    var validation_country_required = "{{ \Helper::language('validation_country_required'); }}";
    var validation_region_required = "{{ \Helper::language('validation_region_required'); }}";
    var validation_area_required = "{{ \Helper::language('validation_area_required'); }}";

    var validation_address_required = "{{ \Helper::language('address_field_is_required'); }}";
    var test = $("#addressEditForm").validate({
        // in 'rules' user have to specify all the constraints for respective fields
        rules: {
            name: {
                required :true,
                maxlength:40,
            },
            phone: {
                required: true,
                minlength: 8,
                maxlength: 15
            },
            country_id: "required",
            region_id: {
                required: true,
            },
            area_id: "required",
            address: "required",
        },
        // in 'messages' user have to specify message as per rules
        messages: {
            name: {
                required:validation_name_required,
                maxlength:validation_name_max_required
            },
            phone: {
                required: validation_phone_required,
                minlength: validation_phone_minlength,
                minlength: validation_phone_maxlength,

            },
            country_id: validation_country_required,
            region_id: validation_region_required,
            area_id: validation_area_required,
            address: validation_address_required,

        },
        submitHandler: function() {
            var form_data = new FormData($('#addressEditForm')[0]);

            // var checkout_page = 1;
            action_url = "{{ route('upadte-address') }}";
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
                    // console.log(response);
                    // return false;
                    $('.loader').css("visibility", "hidden");
                    var url = "{{route('checkout')}}";
                    window.location.href = url;
                },
            });
        }
    });

    $('.EditAddress').click(function(e) {
        e.preventDefault()
        var user_address_id = $(this).siblings(".user_address_id").val();

        var csrf = "{{ csrf_token() }}";

        $.ajax({
            url: "{{ url('/checkoutedit-address') }}" + "/" + user_address_id,
            data: {
                'user_address_id': user_address_id
            },
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            type: "GET",

            beforeSend: function() {
                $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function(response) {
                // return false;
                $('.loader').css("visibility", "hidden");
                $('.addressNewform').addClass('d-none');
                $('.editAddressForm').addClass('d-none');
                $('.addressEditform').removeClass('d-none');
                $(document).find("#addressEditForm").html(response.html);
            },
        });
    })
    // $('.addressNewform').click(function(){
    //     $('.addressNewform').addClass('d-block');
    // });
    function getSubCatList(thisitem) {

        var idCountry = $('#country_id').val();
        var cat_id = $('#cat_id').val();
        //alert(category_id);
        $('#region_id').html('');
        $('#region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
        $('#area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
        $.ajax({
            url: "{{ route('getsubcatlist') }}",
            type: "POST",
            data: {
                id: idCountry,
                cat_id: cat_id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                $('#region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
                $.each(result.sub, function(key, value) {
                    var selected = '';
                    selected = value.country_id == idCountry ? "selected" : "";
                    $("#region_id").append('<option ' + selected + ' value="' + value.id +
                        '">' +
                        value.title + '</option>');
                });
            }
        });
    }

    function getAreaList(thisitem) {

        var idCountry = $('#region_id').val();
        var cat_id = $('#cat_id').val();

        //alert(category_id);
        $('#area_id').html('');
        $('#area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
        $.ajax({
            url: "{{ route('getarealist') }}",
            type: "POST",
            data: {
                id: idCountry,
                cat_id: cat_id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                $('#area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
                $.each(result.sub, function(key, value) {
                    var selected = '';
                    selected = value.area_id == idCountry ? "selected" : "";
                    $("#area_id").append('<option ' + selected + ' value="' + value.id +
                        '">' +
                        value.title + '</option>');
                });
            }
        });
    }

    function SelectedAddressChange(address_id) {

        // var address_id = $("#address_id").val();

        action_url = "{{ route('selected-address') }}";
        var csrf = "{{ csrf_token() }}";
        $.ajax({
            url: action_url,
            data: {
                'address_id': address_id
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
                // console.log(response);
                // return false;
                // console.log('test'+response);
                $('.loader').css("visibility", "hidden");
                var url = "{{route('checkout')}}";
                window.location.href = url;

                // $(document).find("#filterData").modal('show');
            },
        });
    }


    $('#phone').on("input", function() {
        this.value = this.value.replace(/[^0-9\.]/g, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });

    $('#name').on("input", function() {
        console.log(this.value);
        this.value = this.value.replace(/[^a-zA-Z\s]/gi, '');
        $(this).val($(this).val().replace(/^\s+/g, ''));
    });
    var test = $("#addressNewForm").validate({
        // in 'rules' user have to specify all the constraints for respective fields

        rules: {
            name: {
                required :true,
                maxlength:40,
            },
            phone: {
                required: true,
                minlength: 8,
                maxlength: 15
            },

            country_id: "required",
            region_id: {
                required: true,
            },
            area_id: "required",
            address: "required",
        },
        // in 'messages' user have to specify message as per rules
        messages: {
            name: {
                required:validation_name_required,
                maxlength:validation_name_max_required
            },
            phone: {
                required: validation_phone_required,
                minlength: validation_phone_minlength,
                minlength: validation_phone_maxlength,

            },
            country_id: validation_country_required,
            region_id: validation_region_required,
            area_id: validation_area_required,
            address: validation_address_required,
        },
        submitHandler: function() {
            var form_data = new FormData($('#addressNewForm')[0]);

            // var checkout_page = 1;
            action_url = "{{ route('store-address') }}";
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
                    // console.log(response);
                    // return false;
                    $('.loader').css("visibility", "visible");
                    var url = "{{route('checkout')}}";
                    window.location.href = url;
                },
            });
        }
    });
    // var variant_count = {{count(old('prod_variant') ? : []) ? : 1}};
    // var form_data = new FormData('#cheoutForm');
    // alert(form_data);

    // function add_address() {
    //     // alert(212);
    //     let get_address_url = "{{route('addAddress')}}";
    //     $.ajax({
    //         url: get_address_url,
    //         type: 'post',
    //         data: {
    //             data:form_data,
    //         },
    //         success: function(result) {
    //             if (result.success == true) {
    //                 $(".user-address").append(result.html);
    //                 variant_count++;
    //             }
    //         }
    //     });
    // }
    function placeOrder() {

        var purchase_val = $("input[name='purchase_radio']:checked").val();
        var payment_method = $("input[name='payment-method']");
        var user_address_id = $("input[name='address_radio']");
        var store_location_radio = $("input[name='store_location_radio']");
        $("#user_address_error,#payment_method_error,#terms_conditions_error,#store_location_radio_error").text('');
        var coupon_code_id = $("#coupon_code_id").val();
        var coupon_percentage = $("#coupon_percentage").val();
        var delivery_charge = $("#inp-delivery-fee").val();
        var is_buy_now = $('#buy-now').val();
        var pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
        var buy_quantity = $("input[name='buy_quantity[]']").val();
        var buy_discounted_price = $("input[name='buy_discounted_price']").val();
        var buy_org_price = $("input[name='buy_org_price']").val();

        var checkout_user_address = "{{ \Helper::language('checkout_user_address'); }}";
        var checkout_store_address = "{{ \Helper::language('checkout_store_address'); }}";
        var checkout_payment_error = "{{ \Helper::language('checkout_payment_error'); }}";
        var checkout_terms_conditions = "{{ \Helper::language('checkout_terms_conditions'); }}";

        // Gift order
        var gift_error_msg = "Gift Recipient is required";
        var giftRecipient = $("#giftRecipient").val();
        var giftMessage = $("#giftMessage").val();

        var isGiftChecked = $("#userGift").is(":checked");  

        if (isGiftChecked) {  
            if (giftRecipient == "") {
                $("#gift_error").text(gift_error_msg).css('color', 'red');
                return false;  
            } else {
                $("#gift_error").text(''); 
            }
        }

        //Online payment
        if (purchase_val == 1) {
            var error = 0;
            if (!user_address_id.is(":checked")) {
                $("#user_address_error").text(checkout_user_address);
                error++;
            }
        } else {
            var error = 0;
            if (!store_location_radio.is(":checked")) {
                $("#store_location_radio_error").text(checkout_store_address);
                error++;
            }
        }
        if (!payment_method.is(":checked")) {
            $("#payment_method_error").text(checkout_payment_error);
            error++
        }
        if (!$("#terms_conditions").is(":checked")) {
            $("#terms_conditions_error").text(checkout_terms_conditions);
            error++
        }
        if (error != 0) {
            return false;
        }

        //var user_address_id = user_address_id.val();
        var user_address_id = $('input[name="address_radio"]:checked').val();
        var payment_method = $("input[name='payment-method']:checked").val();
        //alert(payment_method);
        var store_location_radio = $("#store_id").val();
        var tax = "{{Helper::Settings('tax')}}";
        $.ajax({
            type: "post",
            data: {
                purchase_type: purchase_val,
                user_address_id: user_address_id,
                payment_method: payment_method,
                store_location_id: store_location_radio,
                coupon_code_id: coupon_code_id,
                coupon_percentage: coupon_percentage,
                delivery_charge: delivery_charge,
                tax: tax,
                is_buy_now:is_buy_now,
                pvariant_Ids:pvariant_Ids,
                buy_quantity:buy_quantity,
                buy_org_price:buy_org_price,
                buy_discounted_price:buy_discounted_price,
                giftRecipient:giftRecipient,
                giftMessage:giftMessage
            },
            url: "{{ route('storeCheckout') }}",
            beforeSend: function() {
                $(".loader").fadeIn();
                $('.loader').css("visibility", "visible");
            },
            success: function(response) {
                $('.loader').css("visibility", "hidden");
                if (response.error == true) {
                    Swal.fire({
                        // icon: "error",
                        title: "",
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var redirectUrl = 'cart/';
                            window.location.href = redirectUrl;
                        }
                    });
                }
                if (response.success == true) {
                    if(payment_method == 1){
                        var redirectUrl = response.redirect_url;
                        window.location.href = redirectUrl;    
                    }else{
                        var redirectUrl = 'thankyou/' + response.order_id;
                        window.location.href = redirectUrl;
                    }
                }

                //$(document).find("#OrderSuccessfully").append(response.html);
                //$('#OrderSuccessfully').modal('show');
            }
        });

    }

    // $(document).on("click", "input[name='address_radio']", function(e) {
    //     var checked = $(this).attr("checked");
    //     if(!checked){
    //         $(this).attr("checked", true);
    //     } else {
    //         $(this).removeAttr("checked");
    //         $(this).prop("checked", false);
    //     }
    // })
    
    $(document).ready(function() {
        if ($("input[name='address_radio']:checked")) {
            var user_address_id = $("input[name='address_radio']").val();
            checkDeliveryAddressByArea(user_address_id);
        }
    });

    function checkDeliveryAddressByArea(user_address_id) {
        var purchase_type = $("input[name='purchase_radio']").val()
        if (purchase_type == 1) {

            var checked = $("#radioAddress"+user_address_id).attr("checked");
            
            if(!checked){
                $("input[name='address_radio']").removeAttr("checked");
                $("#radioAddress"+user_address_id).attr("checked", true);
            } 

            //var user_address = $("input[name='address_radio']");            
            var coupon_code_id = $("#coupon_code_id").val();
            var is_buy_now = $('#buy-now').val();
            var pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
            var buy_quantity = $("input[name='buy_quantity[]']").val();
            //if(user_address.is(':checked')){
            //alert(user_address_id);
            $.ajax({
                type: "post",
                data: {
                    user_address_id: user_address_id,
                    coupon_code_id: coupon_code_id,
                    is_buy_now: is_buy_now,
                    pvariant_Ids: pvariant_Ids,
                    buy_quantity: buy_quantity
                },
                url: "{{ route('userAreaTax') }}",
                success: function(response) {
                    if (response.coupon_code_price != "") {
                        $('#coupon_code_amount_text').text('-' + response.coupon_code_price + "{{Helper::Settings('currency_symbol')}}");
                    }
                    $("#delivery_li").show();
                    $("#delivery_fee").text(response.delivery_fee + " {{Helper::Settings('currency_symbol')}}");
                    $("#inp-delivery-fee").val(response.delivery_fee);
                    $("#grand-total-amount").text(response.grand_total_amount + " {{Helper::Settings('currency_symbol')}}");
                    $("#inp-grand-total-amount").val(response.grand_total_amount);
                }
            });
            //}
        }
    }

    function removeCartItem(variant_id) {
        Swal.fire({
            text: "{{Helper::language('remove_cart_product')}}",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
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

    $(document).ready(function() {
        $('#apply-coupon').on('click', function(e) {
            var coupon_code = $("#coupon").val();
           // var user_address_id = $("input[name='address_radio']").val();
            var user_address_id = $('input[name="address_radio"]:checked').val();
            var is_buy_now = $('#buy-now').val();
            var pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
            var buy_quantity = $("input[name='buy_quantity[]']").val();
            var purchase_type = $("input[name='purchase_radio']:checked").val();
            var coupon_error_msg = "{{ \Helper::language('coupon_field_is_required'); }}"; 

            if (coupon_code == "") {
                $("#coupon_error").text(coupon_error_msg).css('color', 'red');
                return false;
            }
            $.ajax({
                type: "post",
                data: {
                    coupon_code: coupon_code,
                    user_address_id: user_address_id,
                    is_buy_now: is_buy_now,
                    pvariant_Ids: pvariant_Ids,
                    buy_quantity: buy_quantity,
                    purchase_type: purchase_type
                },
                url: "{{ route('applyCoupon') }}",
                success: function(response) {
                    if (response.code_id == "") {
                        $("#coupon_error").text(response.message).css('color', 'red');
                    } else {
                        $("#coupon_error").text(response.message).css('color', 'green');
                        $('#coupon_code_amount_text').text('-' + response.coupon_code_price + " {{Helper::Settings('currency_symbol')}}");
                        $("#li-coupon").show();
                        $("#delivery_li").show();
                        $("#delivery_fee").text(response.delivery_fee + " {{Helper::Settings('currency_symbol')}}");
                        $("#inp-delivery-fee").val(response.delivery_fee);
                        $("#grand-total-amount").text(response.grand_total_amount + " {{Helper::Settings('currency_symbol')}}");
                        $("#inp-grand-total-amount").val(response.grand_total_amount);
                        $("#apply-coupon").hide();
                        $("#clear-coupon").show();
                        $("#coupon_percentage").val(response.coupon_percentage);
                        $("#coupon_code_id").val(response.code_id);
                        $("#tax-amount").text(response.tax_amount + " {{Helper::Settings('currency_symbol')}}");
                    }
                }
            });
        });
    });

    $(document).ready(function() {

        $(".radioPurchase").on("change", function() {
            $(".radioPurchase").removeAttr("checked", "checked");
            var val = $(this).attr("data-class");
            if (val == 'pickup-order-main') {
                var checkout_location_permission_message = "{{ \Helper::language('checkout_location_permission_message'); }}";
                navigator.permissions.query({ name: 'geolocation' })
                .then(permissionStatus => {
                    if (permissionStatus.state === 'granted') {
                        // User has allowed location access
                        $(".location-permission").show();
                        $(".store_na").hide();
                        //console.log('Location access granted');
                    } else if (permissionStatus.state === 'denied' || permissionStatus.state === 'prompt') {
                        Swal.fire({
                            text: checkout_location_permission_message,
                        });
                        // User has denied location access
                        //console.log('Location access denied');                      
                        $(".location-permission").hide();
                        $(".store_na").show();
                        return false;                       
                    } 
                    // else if (permissionStatus.state === 'prompt') {
                    //     // User hasn't made a decision yet; you might request access here
                    //     //console.log('Location permission prompt');
                    //     //alert('Location permission prompt');
                    //     $(".location-permission").hide();
                    //     return false;
                    // }
                })
                .catch(error => {
                    console.error('Error getting permission status:', error);
                });

                $("#delivery_li").hide();
                $(".momo-pay, .card-pay").hide().prop('checked', false);
                var inp_delivery_fee = $("#inp-delivery-fee").val();
                if (inp_delivery_fee != '') {
                    var inp_grand_total_amount = $("#inp-grand-total-amount").val();
                    var grand_total_amount = (inp_grand_total_amount - inp_delivery_fee);
                        grand_total_amount =  parseFloat(grand_total_amount).toFixed(2);
                       // console.log(grand_total_amount)
                    $("#grand-total-amount").text(grand_total_amount + " {{Helper::Settings('currency_symbol')}}");
                }
                $(".contact-information").hide();
                $(".store-contact-information").show();
            } else {
                $(".momo-pay, .card-pay").show();
                $(".contact-information").show();
                $(".store-contact-information").hide();
            }

            $(this).attr("checked", "checked");
            //$("." + val).show();
        });
    });

    function getStoreAddress(storeId) {
        $("#store_id").val(storeId);
    }

    
</script>

<script>
    $(document).ready(function(){
        $('#userGift').change(function() {
            if($(this).is(':checked')) {
                $('#giftDiv').removeClass('d-none'); 
            } else {
                $('#giftDiv').addClass('d-none'); 
                $('#giftRecipient').val('');  
                $('#giftMessage').val('');  
                $('#gift_error').text('');
            }
        });
    });

</script>
@endsection