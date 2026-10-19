@extends('frontend.layouts.app')
@section('title',Helper::language('checkout_label'))
@section('content')
@include('sweetalert::alert')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<style>
    .star {
        color: red;
    }

    .custom
    {
        max-width: 1500px !important;
    }

    .shipping-container {
        width: 435px;
        border: 1px solid #ddd;

        max-width: 100%;
        padding: 15px;
        box-sizing: border-box;
    }

    .shipping-option {
        margin-top: 10px;
        display: flex;
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: -10px;

        /* margin-bottom: 10px; */
        flex-wrap: wrap; 
    }

    .shipping-option label {
        font-weight: bold;
        cursor: pointer;

        white-space: nowrap;
    }

    .shipping-option .price {
        font-weight: normal;

        white-space: nowrap; 
    }

    .shipping-option span {
        display: block;
        font-size: 0.9em; 

        word-wrap: break-word;
    }

    .transit-time {
        margin-left: 15px;  
        font-size: 0.8em; 

        flex-basis: 100%; 
        margin-top: 5px;
    }

    .ship-label
    {
        margin-left: 5px;
    }

    @media (max-width: 480px) {
        .shipping-container {
            width: 100%; 
            padding: 10px;
        }

        .shipping-option {
            flex-direction: column; 
            align-items: flex-start;
        }

        .shipping-option label {
            margin-bottom: 5px; 
        }

        .transit-time {
            font-size: 0.7em;
        }
    }


    .custom-checkbox-label {
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    }

    .custom-checkbox-label input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    width: 21px;
    height: 21px;
    border: 1px solid #858584;
    border-radius: 2px;
    position: relative;
    margin: 0;
    padding: 0;
    background-color: #fff;
    cursor: pointer;
    }

    .custom-checkbox-label input[type="checkbox"]:checked {
    background-color: #FBB516; 
    border-color: #000; 
    }

    .custom-checkbox-label input[type="checkbox"]:checked::before {
    content: "✔";
    position: absolute;
    top: -3px;
    bottom: -1px;
    left: 3px;
    font-size: 14px;
    /* color: #000; */
    color: #fff; 

    }

    .custom-checkbox-label .detail p {
    margin: 0;
    font-size: 16px;
    line-height: 20px;
    }

    .price-list li {
    margin-bottom: 6px;
    font-size: 16px;
    }

    .price-list .original {
        text-decoration: line-through;
        color: #a5a5a5;  
    }

     .price-list .cart-discount {
        color: #a5a5a5;  
    }

    .price-list .discounted {
        color: #28a745;
        font-weight: bold;
    }

    .delivery-display {
        color: #28a745;
        font-weight: bold;
    }

   /* Counter Container */
    .counters {
        position: relative;
        width: 100px;
        height: 36px;
        border-radius: 18px;
        background-color: rgb(36, 36, 36); 
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
    }

    .counters__input {
        border: none;
        width: 60px;
        height: 100%;
        text-align: center;
        color: rgb(251, 181, 22);
        font-size: 16px;
        font-weight: bold;
        background-color: transparent;
        pointer-events: none;
    }

    .counters__increment,
    .counters__decrement {
        position: absolute;
        width: 30px;
        height: 30px;
        top: 3px;
        border-radius: 50%;
        /* background-color: #dddddd; */
        background-color: rgb(36, 36, 36);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .counters__increment {
        right: 3px;
    }

    .counters__decrement {
        left: 3px;
    }

    .counters__increment svg,
    .counters__decrement svg {
        width: 20px;
        height: 20px;
    }

    .counters__increment svg path,
    .counters__decrement svg path {
        fill:  white;
        /* fill:  black; */
    }

    /* reward Points */
     .collapsible-header {
      font-weight: bold;
      cursor: pointer;
      color:  rgb(43, 43, 43);
      margin-bottom: 10px;
    }

    .collapsible-header:hover {
      text-decoration: underline;
    }

    .reward-container {
      max-width: 600px;
      border: 1px solid #ccc;
      padding: 20px;
      border-radius: 8px;
      display: none; 
    }

    .calculation-container
    {
        position: relative;
    }

    .calculation-container .remove-btn
    {
        position: absolute;
        right: 40px;
        top: 36px;
        background: rgb(251, 181, 22);
        color: rgb(36, 36, 36);
        cursor: pointer;
        width: 35%;
        font-weight: 400;
        font-size: 17px;
        line-height: 30px;
        align-items: center;
        transition: 0.4s;
    }

    .calculation-container .remove-btn:hover
    {
        background:  rgb(36, 36, 36);
        color: rgb(251, 181, 22);
    }

    .reward-boxes {
      display: flex;
      gap: 20px;
      margin-bottom: 15px;
    }

    .box {
      flex: 1;
      border: 1px solid #ccc;
      /* padding: 15px; */
      text-align: center;
      border-radius: 6px;
    }

    .box h2 {
      margin: 0;
      font-size: 24px;
      color: #333;
    }

    .box p {
      color: #777;
    }

    .input-row {
        display: flex;
        flex-direction: row;
        margin-top: 5px;
        gap: 5px;
        justify-content: flex-start;
    }

    #points
    {
        padding: 5px;
        width: 200px;
    }

    @media (max-width: 800px) {
        #points {
           width: 95px;
        }
    }

    .amount-display {
      margin-left: 10px;
      font-weight: bold;
      color:  #28a745;
      padding: 5px;
    }

    .clear-display{
      margin-left: 10px;
    }

    .reward-button {
    margin-top: 15px;
    background: rgb(251, 181, 22);
    color: rgb(36, 36, 36);
    cursor: pointer;
    width: 100%;
    font-weight: 400;
    font-size: 16px;
    line-height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-transform: uppercase;
    padding: 12px 30px;
    transition: 0.4s;
    border-radius: 0px;
    border-width: initial;
    border-style: none;
    border-color: initial;
    border-image: initial;
    }

    .reward-button:hover {
      background:  rgb(36, 36, 36);
      color: rgb(251, 181, 22);
    }

    /* add items */
    .new-home .link-button {
    color: #fbb516;
    }


    .new-home .link-button:hover {
    color: rgb(36, 36, 36);
    }

    .new-home {
        display: flex;
        align-items: center;
        justify-content: space-between; /* optional: ensures even spacing */
        flex-wrap: wrap; /* optional: allows wrap on smaller screens */
        gap: 1rem; /* optional: space between form and link */
    }

    .link-button {
        margin-left: auto; /* pushes the link to the right */
        padding-bottom: 30px
    }

     /* Swal */
    .swal2-confirm {
        background: #fbb516 !important; 
        color: black !important;
        border: 1px solid #fbb516 !important;
    }

    .swal2-confirm:focus {
        outline: none !important; 
        /* box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.5) !important;  */
        box-shadow:none !important; 
    }


    .swal-custom-confirm {
        background: #fbb516 !important; 
        color: black !important;
        border: 1px solid #fbb516 !important;
    }

    .swal-custom-confirm:focus {
        outline: none !important; 
        /* box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.5) !important;  */
        box-shadow:none !important; 
    }

    .swal2-icon.swal2-success {
        border-color: black !important;
    }

    .swal2-icon.swal2-warning {
        border-color: #fbb516 !important;
    }

    .swal2-icon.swal2-success .swal2-success-ring {
        border: 4px solid #fbb516 !important;
    }

    .swal2-icon.swal2-success .swal2-success-line-tip,
    .swal2-icon.swal2-success .swal2-success-line-long {
        background-color: #fbb516 !important;
    }

    /* Alert box size */
    .swal-small-popup {
        width: 350px !important;
        padding: 1em !important;
    }

    .swal-small-popup .swal2-title {
        font-size: 1.2em !important;
    }

    .swal-small-popup .swal2-html-container {
        font-size: 1em !important;
    }

    .swal-small-popup .swal2-icon {
        width: 60px !important;
        height: 60px !important;
        font-size: 1.2em !important;
    }

    .swal-small-popup .swal2-icon .swal2-success-line-tip,
    .swal-small-popup .swal2-icon .swal2-success-line-long {
        height: 3px !important;
    }


    /* Coupon alert */
    .coupon-success-box {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 10px;
        padding: 12px 16px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
        margin-top: 15px;
        position: relative;
        transition: all 0.3s ease;
    }

    .icon-circle {
        background-color: #007f3b;
        color: #fff;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        padding: 0;
    }

    .checkmark-icon {
        font-weight: bold;
        line-height: 1;
        transform: translateX(-0.5px); 
    }

    .coupon-success-text {
        flex-grow: 1;
        margin-left: 10px;
    }

    .applied-code {
        font-weight: bold;
        margin-bottom: 2px;
    }
    
    .close-icon {
        cursor: pointer;
        font-size: 20px;
        color: #000;
        font-weight: bold;
        position: absolute;
        right: 12px;
        top: 8px;
    }

    /* reward popup */
   .reward-success-box {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 10px;
        padding: 12px 16px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
        margin-top: 15px;
        position: relative;
        gap: 10px;
        transition: all 0.3s ease;
        }

    .reward-icon-circle {
        background-color: #007f3b;
        color: #fff;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        /* line-height: 1; */
        flex-shrink: 0;
    }

    .reward-checkmark-icon {
        margin: 0;
        padding: 0;
        font-weight: bold;
        font-size: 14px;
        line-height: 1;
    }

    .reward-success-text {
        font-weight: 600;
        font-size: 16px;
        line-height: 1.2;
        display: flex;
        align-items: center;
    }

</style>
<div class="loader" id="loader"></div>
<div class="bread-crumb-block">
    <div class="container custom">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page"> {{@Helper::language('checkout_label')}}</li>
        </ul>
    </div>
</div>

<section class="checkout pt-20 pb-60">
    <div class="container custom">
        <div class="row">
            <div class="col-lg-12">
                <h2>{{@Helper::language('checkout_label')}}</h2>
                <div class="new-home">
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

                    {{--  Add items--}}
                       <a href="{{ route('frontend.home') }}" class="link-button">{{@Helper::language('add_items')}}</a>
                </div>
            </div>
        </div>
        <?php
        // dd($userData);
        ?>
        <div class="online-main purchase-main">
            <div class="row">
                <div class="col-xl-4 col-md-6 checkout-col contact-information">
                    <h5 class="checkout-head">1. Shipping Address / Billing Address</h5>
                    <!-- Before Login Form -->
                    <div class="checkout-heading-flex-group">
                        <h5 class="mb-0">{{@Helper::language('contact_details_web')}}</h5>
                        <?php
                        // dd();
                        ?>
                        <p class="after-login">{{($userData->first_name).' '.($userData->last_name)}} | <a href="mailto:{{@$userData->email}}" class="border-button">{{@$userData->email}}</a></p>


                        <input type="hidden" name="userName" id="userName" value="{{ $userData->first_name }}">
                        <input type="hidden" name="userPhone" id="userPhone" value="{{ $userData->phone }}">

                    </div>

                    <!-- End Before Login Form -->
                    <!-- After Login Form -->
                    <h5 class="mb-2">Shipping Address</h5>
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
                                <label class="custom-checkbox-label">
                                  <input id="radioAddressSix" type="checkbox" name="address_radio" class="addressRadioCurrent" data-class="add-new-address" />
                                  <span class="checkmark"></span>
                                  <span class="detail">
                                    <p class="black-text add_new_address_div">{{@Helper::language('add_new_address_label')}}</p>
                                  </span>
                                </label>
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
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="">{{@Helper::language('area_label_web')}}<span class="text-red star">*</span></label>
                                <select value="{{old('area_id')}}" name="area_id" id="area_id" class="form-select">
                                    <option value="">{{@Helper::language('choose_area_web')}}</option>
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
                                        <label for="user_drop">Hand over to Me</label>
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
                            <button type="submit" class="solid-button w-100">{{@Helper::language('submit_btn')}}</button>

                        </div>
                    </form>
                    <span class="error red-text" id="user_address_error"></span>
                    <!-- End After Login Form -->


                    <!-- Start Edit Form  -->
                    <form class="checkout-form addressEditform d-none" id="addressEditForm">
                    </form>
                    <!-- End Edit Form -->

                    {{-- Existing Bill Address  --}}
                    <h5 class="mb-2 mt-5">Billing Address</h5>
                    <div style="margin-top:20px;margin-bottom:20px;">
                        <div class="check-group">
                            <input class="form-check-input" type="checkbox" value="" id="sameAddress" style=" transform: scale(1.3);margin-right: 10px;border: 1px solid #858584;">
                            <label class="form-check-label"><span style="color: #858584 !important;cursor: default;font-size:20px;">Same as shipping address.</span></label>
                        </div>
                    </div>
                    
                    <div id="billingAddressContainer">
                        @if($UserBillAddressData->isEmpty())
                            <div class="mt-20">
                                <a href="{{ route('add-bill-address') }}" ><u>Add New Billing Address.</u></a>
                            </div>
                        @else
                            @foreach($UserBillAddressData as $key=>$data)
                            <form class="checkout-form">
                                <div class="address-group">
                                        <div class="detail">
                                            <h5>Default Billing address</h5>
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
                                        </div>
                               
                                    <input type="hidden" name="bill_address_id" id="bill_address_id" value="{{@$data->id}}">
                                    <input type="hidden" class="user_bill_address_id" name="user_bill_address_id" id="user_bill_address_id" value="{{@$data->id}}"> 
                                    <a href="javascript:void(0)" class="border-button EditBillAddress">{{@Helper::language('edit_label')}}</a>
                                </div>
                                @endforeach
                            </form>
                        @endif
                    </div>
               

                    {{-- Edit Bill address --}}
                    <form class="checkout-form billAddressEditform d-none" id="billAddressEditForm">
                    </form>

                </div>
                <div class="col-xl-4 col-md-6 checkout-col store-contact-information" style="display: none;">
                    <h5 class="checkout-head">1. {{@Helper::language('store_label_web')}}</h5>
                    <div class="checkout-heading-flex-group">
                        <h5 class="mb-0">{{@Helper::language('contact_details_web')}}</h5>
                        <?php
                        ?>
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
                <h6 id="basket_total" class="toggle1 toggled-on"> </h6>

                <div class="cart-item-list toggled-on">
                    @php
                    $original_price =0;
                    $total_discount_price =0;
                    $product_discount_price = 0;
                    $is_product_discount = false;
                    $freeItems = [];
                    $discountFlag = false;
                    $discountTotal = 0;
                    $total_quantity = 0;
                    $offerCheck=false;
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
                        if($is_buy_now == 0)
                        {
                            $bogo_cart = $result->cart->where('user_id', $user_id)->sortByDesc('id')->first();
                            $is_bogo = $bogo_cart ? $bogo_cart->is_bogo : 0;
                            $is_offer = $bogo_cart ? $bogo_cart->is_offer : 0;
                            $discount_amount = $bogo_cart ? $bogo_cart->discount_amount : null;
                            $offer_type = $bogo_cart ? $bogo_cart->offer_type : null;
                        }
                        else
                        {
                            $is_bogo = $result->is_bogo ?? 0;
                            $is_offer = $result->is_offer ?? 0;
                            $discount_amount = $result->discount_amount ?? null;
                            $offer_type = $result->offer_type ?? null;
                        }

                        $product_unit = Helper::getUnitById($result->variant_uof);
                 
                        if (empty($variant_quantity)) {
                            $qty = Helper::getUserCartQuantity($result->id, $user_id);
                        } else {
                            $qty = $variant_quantity;
                        }

                        $org_price = @$result->variant_price ? ($result->variant_price * $qty) : 0;
                        $discount_price = $org_price;

                        // ✅ Apply offer or variant discount
                        if (!$is_bogo && $is_offer) {
                            $offerCheck=true;
                            if ($offer_type == 'flat') {
                                $discount_price = max(0, $org_price - ($discount_amount * $qty));
                            } elseif ($offer_type == 'percentage') {
                                $discount_price = max(0, $org_price - ($org_price * $discount_amount / 100));
                            }
                            $is_product_discount = true;
                        } elseif ($result->variant_discounted_price && $result->variant_discounted_price != 0) {
                            $discount_price = $result->variant_discounted_price * $qty;
                            $is_product_discount = true;
                        }

                        // ✅ Accumulate totals
                        $original_price += $org_price;
                        $product_discount_price += $discount_price;
                        $total_discount_price += ($org_price - $discount_price);


                        // Bogo check
                        if ($is_bogo) {
                                $freeItems[] = $result;
                            }
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

                                // Update total quantity logic
                                $effective_qty = $is_bogo ? ($qty * 2) : $qty;
                                $total_quantity += $effective_qty;

                                @endphp
                                 <span class="counters mb-0">
                                    <input class="counters__input" type="text"
                                        value="{{$qty}}"
                                        name="counters" size="5" id="qty_ince_{{$result->id}}" readonly="readonly" />
                                    <a class="counters__increment" onclick="quntityIncreaseOrDecrease('{{Helper::encodeUrl($result->get_product_details->id)}}','{{$result->id}}','incr',{{$is_offer ?? 0}});"  href="javascript:void(0)" data-id="{{$result->id}}">
                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="plus">
                                                <path id="Vector"
                                                    d="M19.1999 11.2H12.8V4.79995C12.8 4.35845 12.4416 4 11.9999 4C11.5584 4 11.2 4.35845 11.2 4.79995V11.2H4.79995C4.35845 11.2 4 11.5584 4 11.9999C4 12.4416 4.35845 12.8 4.79995 12.8H11.2V19.1999C11.2 19.6416 11.5584 20 11.9999 20C12.4416 20 12.8 19.6416 12.8 19.1999V12.8H19.1999C19.6416 12.8 20 12.4416 20 11.9999C20 11.5584 19.6416 11.2 19.1999 11.2Z"
                                                    fill="#242424" />
                                            </g>
                                        </svg>
                                    </a>
                                    <a class="counters__decrement" style="cursor: pointer;"  onclick="quntityIncreaseOrDecrease('{{Helper::encodeUrl($result->get_product_details->id)}}','{{$result->id}}','desc',{{$is_offer ?? 0}});">
                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M4.95996 12.9203H19.0399C19.5702 12.9203 20 12.4905 20 11.9601C20 11.4298 19.5703 11 19.0399 11H4.95996C4.4298 11.0001 4 11.4299 4 11.9602C4 12.4905 4.4298 12.9203 4.95996 12.9203Z"
                                                fill="#242424" />
                                        </svg>
                                    </a>
                                </span>
                            </div>
                            <ul>
                                <li class="product-pricing">
                                    @php
                                        $single_original_price = $result->variant_price;
                                        $final_price = $single_original_price;

                                        if (!$is_bogo && $is_offer) {
                                            if ($offer_type == 'flat') {
                                                $final_price = max(0, $single_original_price - $discount_amount);
                                            } elseif ($offer_type == 'percentage') {
                                                $final_price = max(0, $single_original_price - ($single_original_price * $discount_amount / 100));
                                            }
                                        } elseif ($result->variant_discounted_price && $result->variant_discounted_price != 0) {
                                            $final_price = $result->variant_discounted_price;
                                        }
                                    @endphp

                                    @if ($final_price < $single_original_price)
                                        <h5>
                                            {{ $final_price }}{{ Helper::Settings('currency_symbol') }}
                                            <span>{{ $single_original_price }}{{ Helper::Settings('currency_symbol') }}</span>
                                        </h5>
                                    @else
                                        <h5>{{ $single_original_price }}{{ Helper::Settings('currency_symbol') }}</h5>
                                    @endif
                                </li>

                                <li>
                                    <a href="javascript:void(0)" onclick="removeCartItem('{{ $result->id }}')" class="link-button">{{@Helper::language('remove_btn')}}</a>
                                </li>
                                <input type="hidden" name="offer_status[]" value="{{ $is_offer }}">
                                <input type="hidden" name="offer_type[]" value="{{ $offer_type ?? '' }}">
                                <input type="hidden" name="discount_amount[]" value="{{ $discount_amount ?? 0 }}">
                                <input type="hidden" name="bogo_status[]" value="{{ $is_bogo }}">
                                <input type="hidden" value="{{ $result->id }}" name="pvariant_Ids[]">
                                <input type="hidden" value="{{$qty?:0}}" name="buy_quantity[]">
                                <input type="hidden" value="{{@$result->variant_price}}" name="buy_org_price">
                                <input type="hidden" value="{{@$result->variant_discounted_price}}" name="buy_discounted_price">
                            </ul>
                        </div>
                    </div>
                    @endforeach
                    {{-- //loop close/ --}}
            
                     @php
                        if (isset($discountDetails['minimum_amount'], $discountDetails['discount_type'], $discountDetails['discount_value']) && empty($freeItems) && !$offerCheck)
                          {
                            $min = $discountDetails['minimum_amount'];
                            $upto = $discountDetails['upto_amount'];
                            $type = $discountDetails['discount_type'];
                            $value = $discountDetails['discount_value'];

                            if ($product_discount_price >= $min) {
                                $discountFlag = true;

                                if ($type == 'flat') {
                                    $discountTotal = $value;
                                } elseif ($type == 'percentage') {
                                    $rawDiscount = ($value / 100) * $product_discount_price;
                                    $discountTotal = $rawDiscount <= $upto ? $rawDiscount : $upto;
                                }
                            }
                        }

                        $finalPrice = $product_discount_price - $discountTotal;

                    @endphp

                </div>
                <input type="hidden" name="discount_flag" id="discount_flag" value="{{ isset($discountFlag) && $discountFlag ? 1 : 0 }}">
                <input type="hidden" name="discount_amount" id="discount_amount" value="{{ $discountTotal }}">
                <input type="hidden" name="freeItemCount" id="freeItemCount" value="{{ count($freeItems) }}">
                <input type="hidden" value="{{ $total_quantity}}" name="basket_quantity" id="basket_quantity">


                
                <!-- Free Items Section -->
                @if (count($freeItems) > 0)
                    <div class="cart-item-list toggled-on free-item-section">
                        <h5>
                            You have unlocked {{ count($freeItems) }} free {{ count($freeItems) === 1 ? 'item' : 'items' }}! 
                            <i class="fas fa-smile" style="font-size: 20px;"></i>
                        </h5>
                        @foreach ($freeItems as $freeItem)
                            @php
                                $product_title = session::get('language') == 1
                                    ? $freeItem->get_product_details->product_name
                                    : ($freeItem->get_product_details->product_name_fr ?? $freeItem->get_product_details->product_name);
                                $product_image = $freeItem->get_product_details->get_product_images->first();
                                $price_to_show = $freeItem->variant_discounted_price && $freeItem->variant_discounted_price != 0
                                    ? $freeItem->variant_discounted_price
                                    : $freeItem->variant_price;
                            @endphp
                            <div class="single-product">
                                <div class="product-img">
                                     <a href="#">
                                    @if ($product_image && file_exists(public_path('uploads/product/' . $product_image->image)))
                                        <img src="{{ asset('uploads/product/' . $product_image->image) }}" alt="{{ $product_title }}" />
                                    @else
                                        <img src="{{ asset('assets/frontend/images/image-not-avilable.png') }}" alt="Image not available" />
                                    @endif
                                     </a>
                                </div>
                                <div class="product-detail">
                                    <div class="product-detail-main">
                                        <h6>{{ $product_title }}</h6>
                                        <p class="quantity"> {{@Helper::language('volume')}}<span>: {{ @$freeItem->variant_size ? $freeItem->variant_size . ' ' . $product_unit : '' }}</span></p>
                                        @php
                                        if(empty($variant_quantity)){
                                            $qty = Helper::getUserCartQuantity($freeItem->id,$user_id);
                                        }else{
                                            $qty =$variant_quantity;
                                        }
                                        @endphp
                                        <p class="quantity"> Free {{@Helper::language('quantity')}}: <span>{{$qty}}</span></p>
                                    </div>
                                    <ul>
                                        <li class="product-pricing">
                                            <h5>
                                                {{ '0' }} {{Helper::Settings('currency_symbol')}}
                                            </h5>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="form-group mb-30">
                    <div class="loyalty-points-form-group">
                        <label for=""><i class="icon-discount"></i>{{@Helper::language('coupons')}}</label>
                        <input type="text" id="coupon" value="" required="" placeholder="{{@Helper::language('apply_coupons_plac')}}">
                        <button class="link-button remove-btn" id="apply-coupon">{{@Helper::language('apply')}}</button>
                        <a onclick="location.reload();" style="display: none;" class="link-button remove-btn" id="clear-coupon">{{@Helper::language('clear')}}</a>
                        <span class="" id="coupon_error"></span>
                    </div>
                    <div id="coupon-success-box" style="display: none;" class="coupon-success-box">
                        <div class="icon-circle">
                            <i class="checkmark-icon">✔</i>
                        </div>
                        <div class="coupon-success-text">
                            <div id="applied-code" class="applied-code">COUPONCODE</div>
                            <div id="coupon-saved-msg">You just saved 50 GH₵ on your order</div>
                        </div>
                        <div class="close-icon" id="coupon-close">×</div>
                    </div>
                    <div class="invalid-feedback">
                        Please enter
                    </div>
                </div>

                {{-- Reward Points --}}
                <div class="form-group mb-30">
                    <div class="collapsible-header" onclick="toggleRewardSection()">▶ Apply Reward Points </div>

                    <div class="reward-container" id="rewardSection">
                        <div class="reward-boxes">
                            <div class="box">
                            <h2>{{$totalPoints}}</h2>
                            <p><strong>Reward Points</strong><br>You have</p>
                            </div>
                            <div class="box">
                            @if($loyaltyInfo)
                                <h2>{{ Helper::Settings('currency_symbol') }} {{ $loyaltyInfo->redeem_ghs_value / $loyaltyInfo->points_per_ghs }}</h2>
                            @else
                                <h2> - </h2>
                            @endif
                            <p><strong>Per Reward Point</strong><br>Amount</p>
                            </div>
                        </div>

                        <div class="input-group calculation-container" style="margin-bottom: 10px; display: flex; flex-direction: column;">
                            <label for="points" style="font-weight: bold;">Enter Your Reward Points:</label>
                            <div class="input-row">
                                <input type="number" id="points" value="0" oninput="calculateAmount()">
                                <span class="amount-display" id="amountDisplay">equals to {{Helper::Settings('currency_symbol')}} 0.00</span>
                                <span class="clear-display"> <button onclick="location.reload();" style="display: none;" class="remove-btn" id="clear-reward">{{@Helper::language('clear')}}</button></span>
                            </div>
                           
                            <div class="mt-3" id="reward_error"></div>
                        </div>

                        <button class="reward-button" id="reward-button" onclick="applyReward()">APPLY</button>

                        <div id="reward-success-box" class="reward-success-box"  style="display: none;">
                        <div class="reward-icon-circle">
                            <i class="reward-checkmark-icon">✔</i>
                        </div>
                        <div class="reward-success-text" id="reward-success-text">500 Reward Points Redeemed</div>
                        <div class="close-icon" id="reward-close">×</div>
                        </div>
                    </div>
                </div>


                <div class="form-group mb-30">
                    <h6>Gift Message</h6>
                    <!--GiftCard Image -->
                    <div style="margin-bottom: 5px">
                         <img src="{{ asset('assets/frontend/images/Gift-Card_Icon.png') }}" title="Gift-Card" alt="Gift-Card"  width="100" height="80">
                    </div>
                    <div class="check-group terms-conditions" style="display: flex; align-items: center;margin:0px !important;">
                        <input class="form-check-input" type="checkbox" value="" id="userGift">
                        <label class="form-check-label">
                    <span style="color: #858584 !important;cursor: default;">Select this option to include personalized message written on a card, enhancing the uniqueness of your gift.
                        <br><b style="color:black" > The cost will be {{Helper::Settings('delivery_fee')}} {{Helper::Settings('currency_symbol')}}.</b></span></label>
                    </div>
                    <div class="d-none mt-2" id="giftDiv">
                        <label for="">Recipient's Name</label>
                        <input type="text" id="giftRecipient" value="" required="" placeholder="Recipient's Name">
                        <span class="" id="gift_error"></span>
                        <br><br>
                        <label for="">Gift Message</label> 
                        <textarea cols="5" rows="2" name="giftMessage" id="giftMessage" required="" 
                        placeholder="Enter a special message for the recipient here"></textarea>
                        <span class="" id="gift_error2"></span>
                    </div>  
                </div>
                <div class="price-detail">
                    <h6> {{@Helper::language('price_detail_label')}}( {{$total_quantity}} {{@Helper::language('items')}})</h6>
                    <ul class="price-list">

                        <div class="cart-total-summary mb-2">
                            @if($finalPrice < $product_discount_price)
                                {{-- Original price with strike-through --}}
                                <li>
                                    <strong>Total MRP:</strong>
                                    <span class="original">
                                        {{ number_format($product_discount_price, 2) }} {{ Helper::Settings('currency_symbol') }}
                                    </span>
                                </li>

                                <li>
                                    <strong>Cart Discount:</strong>
                                    <span class="cart-discount">
                                        - {{ number_format($discountTotal, 2) }} {{ Helper::Settings('currency_symbol') }}
                                    </span>
                                </li>


                                {{-- Final discounted price --}}
                                <li>
                                    <strong></strong>
                                    <span class="discounted">
                                        {{ number_format($finalPrice, 2) }} {{ Helper::Settings('currency_symbol') }}
                                    </span>
                                </li>
                            @else

                                <li> <strong>Total MRP:</strong> <span>{{@Helper::numberFormat($original_price)}} {{Helper::Settings('currency_symbol')}}</span></li>
                                  @if($total_discount_price>0)
                                    <li>
                                        <span>Discount on MRP:</span>
                                        <span class="discounted">
                                            - {{ number_format($total_discount_price, 2) }} {{ Helper::Settings('currency_symbol') }}
                                        </span>
                                    </li>
                                @endif
                                
                            @endif
                        </div>

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

                        
                        <li id="li-coupon" style="display: none;"><span>Coupon Discount</span> <span id="coupon_code_amount_text"></span></li>
                        <input type="hidden" value="" id="coupon_code_id">
                        <input type="hidden" value="" id="coupon_percentage">
                        <input type="hidden" value="" id="coupon_code_amount">

                        <li id="li-reward" style="display: none;"><span>Reward Discount</span> <span id="reward_amount_text"></span></li>
                        <input type="hidden" value="" id="reward_id">
                        <input type="hidden" value="" id="conversion_rate">
                        <input type="hidden" value="" id="reward_points">

                        <input type="hidden" id="base_total" value="{{ $finalPrice > 0 ? $finalPrice : $product_discount_price }}">
                        <input type="hidden" id="coupon_applied" value="">
                        <input type="hidden" id="coupon_code" value="">
                        <input type="hidden" id="reward_applied" value="">
                        <input type="hidden" id="reward_discount_applied" value="">

                        <li style="display: none;" id="delivery_li"><span id="delivery_charge" class="delivery-display">Delivery Charges</span>
                            <span id="delivery_fee" class="delivery-display"></span>
                            <input type="hidden" id="inp-delivery-fee" value="">
                        </li>

                        <li style="display:none ;" id="gift_charge_li">
                            <span>Personalized Gift Card</span> 
                            <span id="gift_charge_text">{{Helper::Settings('delivery_fee')}} {{Helper::Settings('currency_symbol')}}</span>
                        </li>
                     
                        <input type="hidden" id="page_reload_flag" name="page_reload_flag" value="0">

                    </ul>
                    <div class="total-amount">
                        <h5>{{ @Helper::language('total_amount_label') }}</h5>
                        <h5 id="grand-total-amount">
                            {{ @Helper::numberFormat($finalPrice > 0 ? $finalPrice : $product_discount_price) }} {{ Helper::Settings('currency_symbol') }}
                        </h5>
                        <input type="hidden" id="inp-grand-total-amount" value="{{ $finalPrice > 0 ? $finalPrice : $product_discount_price }}">
                    </div>
                </div>
                <div class="check-group terms-conditions">
                    <input class="form-check-input" id="terms_conditions" name="terms_conditions" type="checkbox" value="">
                    <label class="form-check-label">{{@Helper::language('i_agree_to_the')}} <a target="_blank" href="{{route('termsCondition')}}" class="border-button">{{@Helper::language('terms_&_conditions')}}</a>{{@Helper::language('and_label')}} <a href="{{route('privacyPolicy')}}" target="_blank" class="border-button"> {{@Helper::language('privacy_policy')}}</a>
                        <p><span class="error red-text" id="terms_conditions_error"></span></p>
                    </label>
                </div>
                <input type="hidden" value="{{$is_buy_now?:0}}" id="buy-now">

                <a href="javascript:void(0)" class="solid-button w-100 fw-bold" style="font-size:1.1rem;" onclick="return placeOrder()">{{@Helper::language('place_order_btn')}}</a>
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
        $(document).find("#billAddressEditForm").html('');
        var middleOfPage = $(document).height() / 2;

        // Scroll to the middle of the page with animation
        $('html, body').animate({
            scrollTop: middleOfPage
        }, 1000);

    });

    // Bill Address
    $('.EditBillAddress').click(function() {

        $('#billAddressEditForm').addClass('d-none');
        $(document).find("#addressNewForm").html('');
        $(document).find("#addressEditForm").html('');
        var middleOfPage = $(document).height() / 2;

        // Scroll to the middle of the page with animation
        $('html, body').animate({
            scrollTop: middleOfPage
        }, 1000);

    });

    $('#radioAddressSix').on('change', function () {
        if ($(this).is(':checked')) {
            $('.add-new-address').removeClass('d-none');
            $('.addressNewform').removeClass('d-none');
            $('#addressEditForm').html('');
        } else {
            $('.add-new-address').addClass('d-none');
        }
    });

    var validation_name_required = "{{ \Helper::language('name_field_is_required'); }}";
    var validation_name_max_required = "{{ \Helper::language('the_name_may_not_be_greater_than_40_characters'); }}";
    var validation_phone_required = "{{ \Helper::language('phone_number_field_is_required'); }}";
    var validation_phone_minlength = "{{ \Helper::language('phone_number_min_max'); }}";
    var validation_phone_maxlength = "{{ \Helper::language('phone_number_min_max'); }}";
    var validation_country_required = "{{ \Helper::language('validation_country_required'); }}";
    var validation_region_required = "{{ \Helper::language('validation_region_required'); }}";
    var validation_area_required = "{{ \Helper::language('validation_area_required'); }}";
    var validation_zip_code_required = "{{ \Helper::language('zipcode_field_is_required'); }}";
    var validation_city_required = "{{ \Helper::language('city_field_is_required'); }}";
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
            zip_code: "required",
            city: "required",
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
            zip_code: validation_zip_code_required,
            city: validation_city_required,

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
                $('.loader').css("visibility", "hidden");
                $('.addressNewform').addClass('d-none');
                $('.billAddressEditform').addClass('d-none');
                $('.editAddressForm').addClass('d-none');
                $('.addressEditform').removeClass('d-none');
                $(document).find("#addressEditForm").html(response.html);
            },
        });
    })

    // Bill Adddress
    var test2=$("#billAddressEditForm").validate({
        rules: {
            bill_name: {
                required :true,
                maxlength:40,
            },
            bill_phone: {
                required: true,
                minlength: 8,
                maxlength: 15
            },
            bill_country_id: "required",
            bill_region_id: {
                required: true,
            },
            bill_area_id: "required",
            bill_address: "required",
            bill_zip_code: "required",
            bill_city: "required",
        },
        messages: {
            bill_name: {
                required:validation_name_required,
                maxlength:validation_name_max_required
            },
            bill_phone: {
                required: validation_phone_required,
                minlength: validation_phone_minlength,
                minlength: validation_phone_maxlength,

            },
            bill_country_id: validation_country_required,
            bill_region_id: validation_region_required,
            bill_area_id: validation_area_required,
            bill_address: validation_address_required,
            bill_zip_code: validation_zip_code_required,
            bill_city: validation_city_required,

        },
        submitHandler: function() {
            var form_data = new FormData($('#billAddressEditForm')[0]);

            action_url = "{{ route('update-bill-address') }}";
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
                    $('.loader').css("visibility", "hidden");
                    var url = "{{route('checkout')}}";
                    window.location.href = url;
                },
            });
        }
    });



    $('.EditBillAddress').click(function(e) {
        e.preventDefault()
        var user_address_id = $(this).siblings(".user_bill_address_id").val();

        var csrf = "{{ csrf_token() }}";

        $.ajax({
            url: "{{ url('/checkoutedit-bill-address') }}" + "/" + user_address_id,
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
                $('.loader').css("visibility", "hidden");
                $('.addressNewform').addClass('d-none');
                $('.addressEditform').addClass('d-none');
                $('.billAddressEditform').removeClass('d-none');
                $(document).find("#billAddressEditForm").html(response.html);
            },
        });
    })

    function getSubCatList(thisitem) {

        var idCountry = $('#country_id').val();
        var cat_id = $('#cat_id').val();
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

        function getBillSubCatList(thisitem) {
        var idCountry = $('#bill_country_id').val();
        var cat_id = $('#cat_id').val();   
        

        $('#bill_region_id').html('');
        $('#bill_region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
         $('#bill_area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
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
                $('#bill_region_id').html('<option value="">{{@Helper::language("choose_region_web")}}</option>');
                $.each(result.sub, function(key, value) {
                    var selected = '';
                    selected = value.country_id == idCountry ? "selected" : "";
                    $("#bill_region_id").append('<option ' + selected + ' value="' + value.id +
                        '">' +
                        value.title + '</option>');
                });
            }
        });
    }

    function getBillAreaList(thisitem) {
        var idRegion = $('#bill_region_id').val();
        var cat_id = $('#cat_id').val();
        $('#bill_area_id').html('');
        $('#bill_area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
        $.ajax({
            url: "{{ route('getarealist') }}",
            type: "POST",
            data: {
                id: idRegion,
                cat_id: cat_id,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#bill_area_id').html('<option value="">{{@Helper::language("choose_area_web")}}</option>');
                $.each(result.sub, function(key, value) {
                    var selected = '';
                    selected = value.area_id == idRegion ? "selected" : "";
                    $("#bill_area_id").append('<option ' + selected + ' value="' + value.id +
                        '">' +
                        value.title + '</option>');
                });
            }
        });
    }

    function SelectedAddressChange(address_id) {

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
                $('.loader').css("visibility", "hidden");
                var url = "{{route('checkout')}}";
                window.location.href = url;
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
            zip_code: "required",
            city: "required",
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
            zip_code: validation_zip_code_required,
            city: validation_city_required,
            
        },
        submitHandler: function() {
            var form_data = new FormData($('#addressNewForm')[0]);

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
                    $('.loader').css("visibility", "visible");
                    var url = "{{route('checkout')}}";
                    window.location.href = url;
                },
            });
        }
    });
    function placeOrder() {

        var purchase_val = $("input[name='purchase_radio']:checked").val();
        var payment_method = $("input[name='payment-method']");
        var user_address_id = $("input[name='address_radio']");
        var store_location_radio = $("input[name='store_location_radio']");
        $("#user_address_error,#payment_method_error,#terms_conditions_error,#store_location_radio_error").text('');
        var coupon_code_id = $("#coupon_code_id").val();
        var coupon_percentage = $("#coupon_percentage").val();

        // Rewards
        var reward_id = $("#reward_id").val();
        var conversion_rate = parseFloat($("#conversion_rate").val());
        var reward_points = parseFloat($("#reward_points").val());

        var delivery_charge = $("#inp-delivery-fee").val();
        var is_buy_now = $('#buy-now').val();
        var pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
        var bogo_status = $("input[name='bogo_status[]']").val();
        var offer_status = $("input[name='offer_status[]']").val();
        var discount_amount = $("input[name='discount_amount[]']").val();
        var offer_type = $("input[name='offer_type[]']").val();
        var buy_quantity = $("input[name='buy_quantity[]']").val();
        var buy_discounted_price = $("input[name='buy_discounted_price']").val();
        var buy_org_price = $("input[name='buy_org_price']").val();

        var checkout_user_address = "{{ \Helper::language('checkout_user_address'); }}";
        var checkout_store_address = "{{ \Helper::language('checkout_store_address'); }}";
        var checkout_payment_error = "{{ \Helper::language('checkout_payment_error'); }}";
        var checkout_terms_conditions = "{{ \Helper::language('checkout_terms_conditions'); }}";

        // Gift order
        var gift_error_msg = "Gift Recipient is required";
        var gift_error_message ="Gift Message is required"
        var giftRecipient = $("#giftRecipient").val();
        var giftMessage = $("#giftMessage").val();

        var isGiftChecked = $("#userGift").is(":checked"); 
        var giftAmount=0; 

        if (isGiftChecked) {
            giftAmount={{Helper::Settings('delivery_fee')}};
            let hasError = false;

            if (!giftRecipient.trim()) {
                $("#gift_error").text(gift_error_msg).css('color', 'red');
                hasError = true;
            } else {
                $("#gift_error").text('');
            }


            if (!giftMessage.trim()) {
                $("#gift_error2").text(gift_error_message).css('color', 'red');
                hasError = true;
            } else {
                $("#gift_error2").text('');
            }

            if (hasError) {
                return false;
            }
        }

        // Same Address
        var isSameAddress= $("#sameAddress").is(":checked"); 
      
        // Bill address Id
        var user_bill_address_id=$("#user_bill_address_id").val();

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

        var user_address_id = $('input[name="address_radio"]:checked').val();
        var payment_method = $("input[name='payment-method']:checked").val();
        var store_location_radio = $("#store_id").val();
        var tax = "{{Helper::Settings('tax')}}";


        // Cart Discount 
        let discountFlag = parseFloat($("#discount_flag").val());
        let discountAmount = parseFloat($("#discount_amount").val());

        // Delivery 
        let grandTotalElement = document.getElementById('grand-total-amount');
        let couponAmount=document.getElementById('coupon_code_amount').value;
        let deliveryAmount=document.getElementById('inp-delivery-fee').value;

       if(!couponAmount)
       {
        couponAmount=0;
       }

        let currentTotal = parseAmount(grandTotalElement.innerText)- giftAmount - parseFloat(deliveryAmount)+parseFloat(couponAmount);

        // Check user Details
        var userName = $('#userName').val();
        var userPhone = $('#userPhone').val();

        if (!userName || !userPhone) {
        Swal.fire({
            icon: "warning",
            title: "Missing Information",
            text: "Please Update Profile Information before checking out!",
            customClass: 
            {
                confirmButton: 'swal-custom-confirm',
                popup: 'swal-small-popup'
            }
        }).then(() => {
            location.href = "/edit-profile";
        });
        return false; 
        }

        $.ajax({
            type: "post",
            data: {
                purchase_type: purchase_val,
                user_address_id: user_address_id,
                user_bill_address_id:user_bill_address_id,
                isSameAddress:isSameAddress,
                payment_method: payment_method,
                store_location_id: store_location_radio,
                coupon_code_id: coupon_code_id,
                coupon_percentage: coupon_percentage,
                reward_id:reward_id,
                conversion_rate:conversion_rate,
                reward_points:reward_points,
                delivery_charge: delivery_charge,
                tax: tax,
                is_buy_now:is_buy_now,
                pvariant_Ids:pvariant_Ids,
                bogo_status:bogo_status,
                offer_status:offer_status,
                discount_amount:discount_amount,
                offer_type:offer_type,
                buy_quantity:buy_quantity,
                buy_org_price:buy_org_price,
                buy_discounted_price:buy_discounted_price,
                giftRecipient:giftRecipient,
                giftMessage:giftMessage,
                giftAmount:giftAmount,
                discountFlag:discountFlag,
                discountAmount:discountAmount,
                currentTotal:currentTotal
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
                        var redirectUrl = 'thankyou/' + response.order_id + '/' + response.earnedpoints;
                        window.location.href = redirectUrl;
                    }
                }

            }
        });

    }
    $(document).ready(function() {
        if ($("input[name='address_radio']:checked")) {
            var user_address_id = $("input[name='address_radio']").val();
            checkDeliveryAddressByArea(user_address_id);
        }
    });

    function parseAmount(text) {
            return parseFloat(text.replace(/[^\d.-]/g, '')) || 0;
        }

    function checkDeliveryAddressByArea(user_address_id) {
        var purchase_type = $("input[name='purchase_radio']").val()
        var previousDeliveryFee = parseFloat($("#inp-delivery-fee").val()) || 0;

        if (purchase_type == 1) {

            var checked = $("#radioAddress"+user_address_id).attr("checked");
            
            if(!checked){
                $("input[name='address_radio']").removeAttr("checked");
                $("#radioAddress"+user_address_id).attr("checked", true);
            } 

            var coupon_code_id = $("#coupon_code_id").val();
            var is_buy_now = $('#buy-now').val();
            var pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
            var buy_quantity = $("input[name='buy_quantity[]']").val();
            let grandTotalElement = document.getElementById('grand-total-amount');
            let currentTotal = parseAmount(grandTotalElement.innerText);

            $.ajax({
                type: "post",
                data: {
                    user_address_id: user_address_id,
                    coupon_code_id: coupon_code_id,
                    is_buy_now: is_buy_now,
                    pvariant_Ids: pvariant_Ids,
                    buy_quantity: buy_quantity,
                    currentTotal:currentTotal,
                    previousDeliveryFee: previousDeliveryFee
                },
                url: "{{ route('userAreaTax') }}",
                success: function(response) {
                    if (response.coupon_code_price != "") {
                        $('#coupon_code_amount_text').text('-' + response.coupon_code_price + "{{Helper::Settings('currency_symbol')}}");
                    }
                    let grandTotal = parseFloat(response.grand_total_amount);
                    let deliveryFee = parseFloat(response.delivery_fee);
                    if(deliveryFee>0)
                    {
                        $("#delivery_fee").text(deliveryFee.toFixed(2) + " {{Helper::Settings('currency_symbol')}}");
                        $("#delivery_charge").removeClass('delivery-display');
                        $("#delivery_fee").removeClass('delivery-display');
                    }
                    else
                    {
                         $("#delivery_fee").text("Yay! free delivery");
                    }

                    $("#inp-delivery-fee").val(deliveryFee);
                    $("#grand-total-amount").text(grandTotal.toFixed(2) + " {{Helper::Settings('currency_symbol')}}");
                    $("#inp-grand-total-amount").val(grandTotal.toFixed(2));
                

                    $("#delivery_li").show();
                }
            });
        }
    }

    function removeCartItem(variant_id) {
        Swal.fire({
            text: "{{Helper::language('remove_cart_product')}}",
            showCancelButton: true,
            confirmButtonColor: "#fbb516",
            cancelButtonColor: "rgb(36, 36, 36)",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            customClass: {
                confirmButton: 'swal-custom-confirm',
                cancelButton: 'swal-custom-cancel'
            }

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
            var user_address_id = $('input[name="address_radio"]:checked').val();
            var is_buy_now = $('#buy-now').val();
            var pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
            var buy_quantity = $("input[name='buy_quantity[]']").val();
            var bogo_status = $("input[name='bogo_status[]']").val();
            var purchase_type = $("input[name='purchase_radio']:checked").val();
            var coupon_error_msg = "{{ \Helper::language('coupon_field_is_required'); }}"; 
            let grandTotalElement = document.getElementById('grand-total-amount');
            let currentTotal = parseAmount(grandTotalElement.innerText);

            const offerInputs = document.querySelectorAll('input[name="offer_status[]"]');
            const offer_status = Array.from(offerInputs).map(input => input.value);

            // Offer Check
            if(offer_status.includes('1') && coupon_code!='')
            {
                Swal.fire({
                        icon: 'warning',
                        title: 'Offer Already Applied',
                        text: "An existing offer is already applied to your cart. Promo codes cannot be used with other offers.",
                        customClass: 
                        {
                            confirmButton: 'swal-custom-confirm',
                            popup: 'swal-small-popup'
                        }
                    });
                    return false;
            }

            
            // Bogo check
            var freeItemCount=$("#freeItemCount").val();

            if (freeItemCount > 0  && coupon_code!='') {
                Swal.fire({
                    icon: 'warning',
                    title: 'BOGO Offer Already Applied',
                    text: 'An existing offer is already applied to your cart. Promo codes cannot be used with other offers.',
                     customClass: 
                    {
                        confirmButton: 'swal-custom-confirm',
                        popup: 'swal-small-popup'
                    }
                });
                return false;
            }


            // Delivery Fee remove from Total
            let deliveryFee=parseFloat(document.getElementById('inp-delivery-fee').value);
            currentTotal=currentTotal-deliveryFee;

            // Gift price remove from Total
            var isGiftChecked = $("#userGift").is(":checked"); 

            if(isGiftChecked)
            {
                currentTotal=currentTotal-{{Helper::Settings('delivery_fee')}};
            }

            // Cart Discount
            var discountFlag = $("#discount_flag").val();
            if (discountFlag == '1') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cart Discount Already Applied',
                    text: 'A discount is already applied to your cart. You cannot apply a promo code.',
                    customClass: 
                    {
                        confirmButton: 'swal-custom-confirm',
                        popup: 'swal-small-popup'
                    }
                });
                return false;
            }

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
                    bogo_status:bogo_status,
                    purchase_type: purchase_type,
                    currentTotal:currentTotal,
                },
                url: "{{ route('applyCoupon') }}",
                success: function(response) {
                    let deliveryFee = parseFloat(response.delivery_fee);
                    let grandTotal = parseFloat(response.grand_total_amount);

                    if(isGiftChecked)
                    {
                        grandTotal+={{Helper::Settings('delivery_fee')}};
                    }

                    if (response.code_id == "") {
                        $("#coupon_error").text(response.message).css('color', 'red');
                    } else {
                        $('#coupon_code_amount_text').text('-' + response.coupon_code_price + " {{Helper::Settings('currency_symbol')}}");
                        $("#li-coupon").show();
                        $("#delivery_li").show();

                        if(deliveryFee>0)
                        {
                            $("#delivery_fee").text(deliveryFee.toFixed(2) + " {{Helper::Settings('currency_symbol')}}");
                            $("#delivery_charge").removeClass('delivery-display');
                            $("#delivery_fee").removeClass('delivery-display');
                        }
                        else
                        {
                            $("#delivery_fee").text("Yay! free delivery");
                        }
                        $("#inp-delivery-fee").val(deliveryFee);
                        $("#grand-total-amount").text((grandTotal.toFixed(2)) + " {{Helper::Settings('currency_symbol')}}");
                        $("#inp-grand-total-amount").val((grandTotal.toFixed(2)));
                        $(".loyalty-points-form-group").hide(); 
                        $("#coupon-success-box").show();

                        // *******
                        $("#coupon_applied").val("1");
                        $("#coupon_code").val(coupon_code);
                        // *******


                        $("#applied-code").text(coupon_code);
                        $("#coupon-saved-msg").html(`You just saved <strong>${response.coupon_code_price} {{Helper::Settings('currency_symbol')}}</strong> on your order`);

                        $("#coupon_percentage").val(response.coupon_percentage);
                        $("#coupon_code_id").val(response.code_id);
                        $("#coupon_code_amount").val(parseFloat(response.coupon_code_price));
                        $("#tax-amount").text(response.tax_amount + " {{Helper::Settings('currency_symbol')}}");

                       Swal.fire({
                            icon: 'success',
                            title: `<span style="color: #FBB516; font-weight: bold;">${coupon_code}</span> Applied!`,
                            html: `Hurray! You saved <strong>${response.coupon_code_price} {{Helper::Settings('currency_symbol')}}</strong> from this coupon.`,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            customClass: {
                                confirmButton: 'swal-custom-confirm'
                            }
                        });

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
                    } else if (permissionStatus.state === 'denied' || permissionStatus.state === 'prompt') {
                        Swal.fire({
                            text: checkout_location_permission_message,
                        });
                        // User has denied location access
                        $(".location-permission").hide();
                        $(".store_na").show();
                        return false;                       
                    } 
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
        });
    });

    function getStoreAddress(storeId) {
        $("#store_id").val(storeId);
    }

    
</script>

{{-- Gift --}}
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

{{-- sames address --}}
<script>
     document.addEventListener("DOMContentLoaded", function () {
        const sameAddressCheckbox = document.getElementById('sameAddress');
        const billingContainer = document.getElementById('billingAddressContainer');

        sameAddressCheckbox.addEventListener('change', function () {
            if (this.checked) {
                billingContainer.style.display = 'none';
                $('#billAddressEditForm').addClass('d-none');
            } else {
                billingContainer.style.display = 'block';
            }
        });
    });
</script>

<!-- Gift card Charges -->
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const giftCheckbox = document.getElementById('userGift');
            const giftDiv = document.getElementById('giftDiv');
            const giftChargeLi = document.getElementById('gift_charge_li');
            const grandTotalElement = document.getElementById('grand-total-amount');
            const grandTotalInput = document.getElementById('inp-grand-total-amount');
    
            const giftCharge = {{Helper::Settings('delivery_fee')}};
    
            function parseAmount(text) {
                return parseFloat(text.replace(/[^\d.-]/g, '')) || 0;
            }
    
            function formatAmount(amount) {
                return amount.toFixed(2) + ' GH₵';
            }
    
            giftCheckbox.addEventListener('change', function () {
                let currentTotal = parseAmount(grandTotalElement.innerText);
    
                if (this.checked) {
                    giftDiv.classList.remove('d-none');
                    giftChargeLi.style.display = 'flex';
    
                    currentTotal += giftCharge;
                } else {
                    giftDiv.classList.add('d-none');
                    giftChargeLi.style.display = 'none';
    
                    currentTotal -= giftCharge;
                }
    
                grandTotalElement.innerText = formatAmount(currentTotal);
                grandTotalInput.value = currentTotal.toFixed(2);
            });
        });
</script>


{{-- Cart Increment --}}
<script>
    function quntityIncreaseOrDecrease(product_id, variantId, type,is_offer) {
        const isBuyNow = $('#buy-now').val() == '1';
        let qtyInput = $("#qty_ince_" + variantId);
        let currentQty = parseInt(qtyInput.val());

        if (type === 'incr') {
            currentQty += 1;
        } else if (type === 'desc') {
            if (currentQty === 1) return false;
            currentQty -= 1;
        }

        qtyInput.val(currentQty); 

        const _token = $('meta[name="csrf-token"]').attr('content');

        if (isBuyNow) {
            // Update Buy Now session quantity
            $.post({
                url: '/buy-now/update-quantity',
                data: {
                    quantity: currentQty,
                    _token: _token
                },
                success: function (response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        } else {
            // Update Cart item
            $.ajax({
                type: "POST",
                url: "{{ route('cartincrement') }}",
                data: {
                    product_id: product_id,
                    quantity: currentQty,
                    variantId: variantId,
                    offer_status: is_offer,
                    _token: _token
                },
                success: function (response) {
                    if (response.success === "true") {
                        location.reload();
                    }
                }
            });
        }
    }



</script>

{{-- rewards --}}
<script>
  function toggleRewardSection() {
    const section = document.getElementById("rewardSection");
    const header = document.querySelector(".collapsible-header");

    if (section.style.display === "none" || section.style.display === "") {
      section.style.display = "block";
      header.innerHTML = "▼ Apply Reward Points";
    } else {
      section.style.display = "none";
      header.innerHTML = "▶ Apply Reward Points";
    }
  }

  function calculateAmount() {
    $("#amountDisplay").show();
    const maxPoints = {{ $totalPoints }};
    const conversionRate = {{ $loyaltyInfo ? $loyaltyInfo->redeem_ghs_value / $loyaltyInfo->points_per_ghs : 0 }};
    const currencySymbol = `{{ Helper::Settings('currency_symbol') }}`;

    const pointsInput = document.getElementById('points').value;
    let points = parseFloat(pointsInput);

    if (isNaN(points) || points < 0) {
      points = 0;
    } else if (points > maxPoints) {
      points = maxPoints;
      document.getElementById('points').value = maxPoints; 
    }

    const amount = points * conversionRate;

    document.getElementById('amountDisplay').textContent = `equals to ${currencySymbol} ${amount.toFixed(2)}`;
  }
</script>

{{-- Apply reward Points --}}
<script>
    function applyReward()
    {
          var reward_points = $("#points").val();
            var user_address_id = $('input[name="address_radio"]:checked').val();
            var is_buy_now = $('#buy-now').val();
            var pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
            var buy_quantity = $("input[name='buy_quantity[]']").val();
            var purchase_type = $("input[name='purchase_radio']:checked").val();
            var reward_error_msg = "Reward Points Field is required"; 
            let grandTotalElement = document.getElementById('grand-total-amount');
            let currentTotal = parseAmount(grandTotalElement.innerText);
            
            // Delivery Fee remove from Total
            let deliveryFee=parseFloat(document.getElementById('inp-delivery-fee').value);
            currentTotal=currentTotal-deliveryFee;

            // Gift price remove from Total
            var isGiftChecked = $("#userGift").is(":checked"); 

            if(isGiftChecked)
            {
                currentTotal=currentTotal-{{Helper::Settings('delivery_fee')}};
            }

            if (reward_points === "" || parseFloat(reward_points) <= 0) {
                $("#reward_error").text("Reward points must be greater than 0").css('color', 'red');
                return false;
            }

             // Cart Discount
            var discountFlag = $("#discount_flag").val();
            if (discountFlag == '1') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cart Discount Already Applied',
                    text: 'A discount is already applied to your cart. You cannot apply reward Points.',
                    customClass: 
                    {
                        confirmButton: 'swal-custom-confirm',
                        popup: 'swal-small-popup'
                    }
                });
                return false;
            }

            $.ajax({
                type: "post",
                data: {
                    reward_points: reward_points,
                    user_address_id: user_address_id,
                    is_buy_now: is_buy_now,
                    pvariant_Ids: pvariant_Ids,
                    buy_quantity: buy_quantity,
                    purchase_type: purchase_type,
                    currentTotal:currentTotal,
                },
                url: "{{ route('applyReward') }}",
                success: function(response) {
                    let deliveryFee = parseFloat(response.delivery_fee);
                    let grandTotal = parseFloat(response.grand_total_amount);

                    if(isGiftChecked)
                    {
                        grandTotal+={{Helper::Settings('delivery_fee')}};
                    }

                    if (response.reward_id == "") {
                        $("#reward_error").text(response.message).css('color', 'red');
                    } else {
                        $('#reward_amount_text').text('-' + response.rewardAmount + " {{Helper::Settings('currency_symbol')}}");
                        $("#li-reward").show();
                        $("#delivery_li").show();

                        if(deliveryFee>0)
                        {
                            $("#delivery_fee").text(deliveryFee.toFixed(2) + " {{Helper::Settings('currency_symbol')}}");
                            $("#delivery_charge").removeClass('delivery-display');
                            $("#delivery_fee").removeClass('delivery-display');
                        }
                        else
                        {
                            $("#delivery_fee").text("Yay! free delivery");
                        }
                        $("#inp-delivery-fee").val(deliveryFee.toFixed(2));

                        $("#grand-total-amount").text((grandTotal.toFixed(2)) + " {{Helper::Settings('currency_symbol')}}");
                        $("#inp-grand-total-amount").val((grandTotal.toFixed(2)));

                        // ********
                        $("#reward_applied").val("1");
                        $("#reward_discount_applied").val(response.rewardAmount);
                        // ********
                        $("#reward-button").hide();
                        $("#amountDisplay").hide();
                        $('#clear-display').show();
                        $("#clear-reward").show();
                        $("#conversion_rate").val(response.conversionRate);
                        $("#reward_id").val(response.reward_id);
                        $("#reward_points").val(parseFloat(response.reward));
                        $("#tax-amount").text(response.tax_amount + " {{Helper::Settings('currency_symbol')}}");

                        $(".calculation-container").hide();
                        $("#reward-button").hide();
                        $("#reward-success-box").show();
                        $("#reward-success-text").text(parseInt(reward_points) + " Reward Points Redeemed");
                    }   
                }
            });
    }
</script>

<script>
        function isNumberKey(evt) {
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

                return false;
            return true;
        }
</script>

{{-- Basket Total --}}
<script>
    let basket_quantity=document.getElementById('basket_quantity').value;
    let basket_total=document.getElementById('basket_total');

    basket_total.innerHTML=`Basket Total ( ${basket_quantity} items)`
</script>

<script>
    let hasMovedToCart = false;

    // Detect reload (type = "reload")
    const navEntry = performance.getEntriesByType("navigation")[0];
    const isReload = navEntry?.type === "reload";

    // Skip this page load from triggering beacon
    if (!isReload) {
        sessionStorage.setItem('buyNowInitialLoad', 'false');
    }

    function moveBuyNowToCartSafely(reason = '') {
        const alreadyMoved = sessionStorage.getItem('buyNowMoved') === 'true';
        const isInitialLoad = sessionStorage.getItem('buyNowInitialLoad') === 'false';

        if (hasMovedToCart || alreadyMoved || isReload || !isInitialLoad) {
            return;
        }

        const data = new FormData();
        data.append('source', 'beacon');

        // Send after 100–200ms delay
        setTimeout(() => {
            navigator.sendBeacon("{{ url('/move-buy-now-to-cart') }}", data);
            hasMovedToCart = true;
            sessionStorage.setItem('buyNowMoved', 'true');
        }, 200);
    }

    window.addEventListener('beforeunload', () => moveBuyNowToCartSafely('beforeunload'));
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            moveBuyNowToCartSafely('visibilitychange');
        }
    });
</script>

<script>
    const navEntry = performance.getEntriesByType("navigation")[0];
    if (navEntry?.type === "reload") {
        document.getElementById('page_reload_flag').value = '1';
    }
</script>

<script>
    // Closing the reward close
        function applyCouponAgain(couponCode) {
            let user_address_id = $('input[name="address_radio"]:checked').val();
            let is_buy_now = $('#buy-now').val();
            let pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
            let buy_quantity = $("input[name='buy_quantity[]']").val();
            let purchase_type = $("input[name='purchase_radio']:checked").val();
         
            let grandTotal = document.getElementById('base_total');
            let currentTotal = parseFloat(grandTotal.value);

            // Delivery Fee remove from Total
            let deliveryFee=parseFloat(document.getElementById('inp-delivery-fee').value);

            // Gift
            var isGiftChecked = $("#userGift").is(":checked"); 

            $.ajax({
                type: "post",
                data: {
                    coupon_code: couponCode,
                    user_address_id: user_address_id,
                    is_buy_now: is_buy_now,
                    pvariant_Ids: pvariant_Ids,
                    buy_quantity: buy_quantity,
                    purchase_type: purchase_type,
                    currentTotal: currentTotal
                },
                url: "{{ route('applyCoupon') }}",
                success: function(response) {
                    let deliveryFee = parseFloat(response.delivery_fee);
                    let grandTotal = parseFloat(response.grand_total_amount);

                    if(isGiftChecked)
                    {
                        grandTotal+={{Helper::Settings('delivery_fee')}};
                    }

                    if (response.code_id != "") {
                        if(deliveryFee>0)
                        {
                            $("#delivery_fee").text(deliveryFee.toFixed(2) + " {{Helper::Settings('currency_symbol')}}");
                            $("#delivery_charge").removeClass('delivery-display');
                            $("#delivery_fee").removeClass('delivery-display');
                        }
                        else
                        {
                            $("#delivery_fee").text("Yay! free delivery");
                        }
                        $("#inp-delivery-fee").val(deliveryFee);

                        $("#coupon_applied").val("1");
                        $("#coupon_code").val(couponCode);

                        $("#coupon_code_amount_text").text('-' + response.coupon_code_price + " {{Helper::Settings('currency_symbol')}}");
                        $("#grand-total-amount").text((grandTotal.toFixed(2)) + " {{Helper::Settings('currency_symbol')}}");
                        $("#inp-grand-total-amount").val((grandTotal.toFixed(2)));
                        $("#coupon_percentage").val(response.coupon_percentage);
                        $("#coupon_code_id").val(response.code_id);
                        $("#coupon_code_amount").val(response.coupon_code_price);
                        $("#tax-amount").text(response.tax_amount + " {{Helper::Settings('currency_symbol')}}");
                    }
                }
            });
        }


      function recalculateTotalWithoutReward() {
        let grandTotal = document.getElementById('base_total');
        let currentTotal = parseFloat(grandTotal.value);

        var isGiftChecked = $("#userGift").is(":checked"); 
        if(isGiftChecked)
        {
            currentTotal=currentTotal+{{Helper::Settings('delivery_fee')}};
        }

        // Delivery Charge
        let deliveryAmount=document.getElementById('inp-delivery-fee').value;
        if(deliveryAmount>0)
        {
            currentTotal=currentTotal+parseInt(deliveryAmount);
        }

        // Add back reward discount since it's removed now
        let rewardDiscount = parseFloat($("#reward_discount_applied").val()) || 0;
        let newTotal = currentTotal + rewardDiscount;

        $("#grand-total-amount").text(newTotal.toFixed(2) + " {{Helper::Settings('currency_symbol')}}");
        $("#inp-grand-total-amount").val(newTotal.toFixed(2));
    }

    $("#reward-close").on("click", function () {
        const currencySymbol = `{{ Helper::Settings('currency_symbol') }}`;
        const section = document.getElementById("rewardSection");
        const header = document.querySelector(".collapsible-header");

        $('#reward-success-box').hide();
        $("#amountDisplay").hide();
        $("#points").val('');
        $("#reward-button").show();
        $(".calculation-container").show();
        $("#clear-reward").hide();
        $("#reward_applied").val("");
        $("#reward_discount_applied").val("");

        $("#conversion_rate").val("");
        $("#reward_id").val("");
        $("#reward_points").val("");
        $("#li-reward").hide();

        section.style.display = "none";
        header.innerHTML = "▶ Apply Reward Points";

        let couponCode = $("#coupon_code").val();
        if (couponCode) {
            applyCouponAgain(couponCode);
        } else {
            recalculateTotalWithoutReward();
        }
    });

</script>

<script>
    function applyRewardAgain(rewardPoints) {
        let user_address_id = $('input[name="address_radio"]:checked').val();
        let is_buy_now = $('#buy-now').val();
        let pvariant_Ids = $("input[name='pvariant_Ids[]']").val();
        let buy_quantity = $("input[name='buy_quantity[]']").val();
        let purchase_type = $("input[name='purchase_radio']:checked").val();

        let grandTotal = document.getElementById('base_total');
        let currentTotal = parseFloat(grandTotal.value);

        // Delivery Fee remove from Total
        let deliveryFee=parseFloat(document.getElementById('inp-delivery-fee').value);

        // Gift
        var isGiftChecked = $("#userGift").is(":checked"); 

        $.ajax({
            type: "post",
            url: "{{ route('applyReward') }}",
            data: {
                reward_points: rewardPoints,
                user_address_id: user_address_id,
                is_buy_now: is_buy_now,
                pvariant_Ids: pvariant_Ids,
                buy_quantity: buy_quantity,
                purchase_type: purchase_type,
                currentTotal: currentTotal
            },
            success: function(response) {
                let deliveryFee = parseFloat(response.delivery_fee);
                let grandTotal = parseFloat(response.grand_total_amount);

                if(isGiftChecked)
                {
                    grandTotal+={{Helper::Settings('delivery_fee')}};
                }

                if (response.reward_id) {
                    if(deliveryFee>0)
                    {
                        $("#delivery_fee").text(deliveryFee.toFixed(2) + " {{Helper::Settings('currency_symbol')}}");
                        $("#delivery_charge").removeClass('delivery-display');
                        $("#delivery_fee").removeClass('delivery-display');
                    }
                    else
                    {
                        $("#delivery_fee").text("Yay! free delivery");
                    }
                    $("#inp-delivery-fee").val(deliveryFee.toFixed(2));

                    // update reward UI again, just like in your applyReward()
                    $("#reward_amount_text").text('-' + response.rewardAmount + " {{Helper::Settings('currency_symbol')}}");
                     $("#grand-total-amount").text((grandTotal.toFixed(2)) + " {{Helper::Settings('currency_symbol')}}");
                    $("#inp-grand-total-amount").val((grandTotal.toFixed(2)));
                    $("#tax-amount").text(response.tax_amount + " {{Helper::Settings('currency_symbol')}}");

                    $("#reward_applied").val("1");
                    $("#reward_points_applied").val(rewardPoints);

                    $("#reward-success-box").show();
                    $("#reward-success-text").text(parseInt(rewardPoints) + " Reward Points Redeemed");
                }
            }
        });
    }


    // Closing the coupon
    function recalculateTotalWithoutCoupon() {
        let grandTotal = document.getElementById('base_total');
        let currentTotal = parseFloat(grandTotal.value);

        var isGiftChecked = $("#userGift").is(":checked"); 
        if(isGiftChecked)
        {
            currentTotal=currentTotal+{{Helper::Settings('delivery_fee')}};
        }

        // Delivery Charge
        let deliveryAmount=document.getElementById('inp-delivery-fee').value;
        if(deliveryAmount>0)
        {
            currentTotal=currentTotal+parseInt(deliveryAmount);
        }

        // Add back coupon discount since it’s removed now
        let couponDiscount = parseFloat($("#coupon_code_amount").val()) || 0;
        let newTotal = currentTotal + couponDiscount;

        $("#grand-total-amount").text(newTotal.toFixed(2) + " {{Helper::Settings('currency_symbol')}}");
        $("#inp-grand-total-amount").val(newTotal.toFixed(2));
    }


    $('#coupon-close').on('click', function() {
        $('#clear-coupon').hide(); 
        $('#coupon-success-box').hide();
        $('.loyalty-points-form-group').show();
        
        $('#li-coupon').hide();
        $("#coupon_code_id").val("");
        $("#coupon_percentage").val("");
        $("#coupon_code_amount").val("");

        $("#coupon_applied").val("");
        $("#coupon_code").val('');
        $("#coupon").val('');

        let rewardPoints = $("#reward_points").val();
        if (rewardPoints && parseFloat(rewardPoints) > 0) {
            applyRewardAgain(rewardPoints);
        } else {
            recalculateTotalWithoutCoupon();
        }

    });

</script>

@endsection