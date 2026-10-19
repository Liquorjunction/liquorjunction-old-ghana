@extends('frontEnd.layouts.new_app')
@section('title','Order Detail')
@section('content')
@include('sweetalert::alert')
<style>
    .storename{
        color:gray;
    }
</style>
  <main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">{{@Helper::language('home')}}</a></li>
                    <li><a href="{{route('myOrder')}}" class="text-grey body-normal">{{Helper::language('my_order')}}</a></li>
                    <li><p class="text-black body-normal">{{Helper::language('order_detail')}}</p></li>
                </ul>
            </div>
        </div>        
        <section class="account pt-30 py-80">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        @include('frontend.layouts.account-sidebar')
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <?php

                         $order_palce_date = \Helper::converttimeTozone($orderData->created_at);
                         $order_palce_date = date("d F Y",strtotime($order_palce_date));
                         $supplierData = DB::table('main_users')->where('id',$orderData->supplier_id)->first();

                            $order_type = "";

                            if ($orderData->order_type == 1) {
                                $order_type = "Purchase Online";
                            }else{
                                $order_type = "Purchase Offline";

                            }

                            if ($orderData->order_type == 1) {
                                $payment_method = "Online";
                            }else{
                                $payment_method = "Offline";

                            }
                        ?>
                        <h1>{{Helper::language('order_detail')}}</h1>
                        <div class="row">
                            <div class="col-12">
                                <div class="common-card order-list order-details">
                                    <ul class="order-detail">
                                        <li>
                                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('order_placed')}}</span>
                                            <span class="body-normal text-dark-grey d-block mb-0">{{@$order_palce_date}}</span>
                                        </li>
                                        <li>
                                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('order_type')}}</span>
                                            <span class="body-normal text-dark-grey d-block mb-0">{{@$order_type}}</span>
                                        </li>
                                        <li class="flex-lg-fill">
                                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('ship_to')}}</span>
                                            <span class="body-normal text-dark-grey d-block mb-0">{{@$orderData->ship_to}}</span>
                                        </li>
                                        <li>
                                            <span class="body-normal text-dark-grey d-block mb-1"> {{Helper::language('order')}}#{{@$orderData->order_id}}</span>
                                            <span class="body-normal text-black text-bold d-block mb-0">{{Helper::language('payment_method_label')}} :<span class="body-normal text-black text-bold d-inline-block ms-1">{{@$payment_method}}</span></span>
                                        </li>
                                    </ul>
                                    <div class="order-details-info">
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-6 address-column">
                                                <div class="common-card account-address1">
                                                    @if($orderData->order_type == 1)
                                                    <h5 class="">{{Helper::language('default_billing_address')}}</h5>
                                                    <p class="body-large text-black">{{@$orderData->ship_to}}</p>
                                                    <address class="body-large">{{@$orderData->address}} {{@$orderData->city}} {{@$orderData->state}} {{@$orderData->zip_code}} {{@$orderData->country}}</address>
                                                    <a href="tel:+{{@$orderData->address_phone}}" class="body-large">{{@$orderData->address_phone}}</a>                                                 
                                                    @else
                                                    <h5 class="">{{Helper::language('pickup_address')}}</h5>

                                                     <p class="body-large text-black">{{@$supplerCheck->name}}</p>
                                                    <address class="body-large">{{@$supplerCheck->street_address}} {{@$supplerCheck->city}} {{@$supplerCheck->states}} {{@$supplerCheck->post_code}} {{@$supplerCheck->country}}</address>
                                                    <a href="tel:+{{@$supplerCheck->phone}}" class="body-large">{{@$supplerCheck->phone}}</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-6 address-column">
                                               
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <ul class="order-summary-list mb-0">
                                                    <li>
                                                        <span class="heading-six mb-0">{{Helper::language('order_summary')}}</span>
                                                    </li>
                                                    <li>
                                                        <span class="body-large text-dark-grey">{{Helper::language('total_price')}}</span>
                                                        <span class="body-large text-dark-grey">{{@$setting->currency_symbol}}{{@$orderData->total_amount}}</span>
                                                    </li>
                                                    <li>
                                                        <span class="body-large text-dark-grey">{{Helper::language('discount_label')}}</span>
                                                        <span class="body-large text-dark-grey">-{{@$setting->currency_symbol}}{{@$discount_price_data}}</span>
                                                    </li>
                                                    <li>
                                                        <span class="heading-five mb-0">{{Helper::language('total_amount_label')}}</span>
                                                        <span class="heading-five mb-0">{{@$setting->currency_symbol}}{{@$orderData->payable_amount}}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>                                                                       
                                </div>
                            </div>
                            <div class="col-lg-7 mt-30">                                
                                <ul class="common-card recent-order order-list order-detail-list">
                                    <li>
                                        <div class="order-detail-heading">
                                            <h4 class="mb-0">{{Helper::language('total_Item	')}}({{@$orderDetailsCount}})</h4>
                                            
                                        </div>                                        
                                        <p class="mb-0"> {{Helper::language('sold_by')}}: <a href="#" class="text-grey storename">{{@$supplierData->store_name}}</a></p>
                                        <p class="samll-text">{{@$supplierData->street_address}} {{@$supplierData->city}} {{@$supplierData->states}} {{@$supplierData->country}}</p>
                                    </li>

                                    <li>
                                        <ul class="order-listing">
                                             @foreach($orderDetails as $details)
                                            <li>
                                                <div class="mini-product-top">
                                                    <div class="product-img">
                                                        <a href="product-detail.php">
                                                            <img src="{{ asset('uploads/product/').'/'.$details->product_image }}" alt="product img" title="Dulux Aquanamel" />
                                                        </a>
                                                    </div>                                
                                                    <div class="product-detail">
                                                        <a href="{{route('productdetails',['id'=>$details->product_id])}}" class="heading-six mb-1">{{@$details->product_name}}</a>
                                                        <p class="body-normal text-black mb-0"> {{Helper::language('quantity')}} : <span class="body-normal text-dark-grey mb-0 ms-1">{{@$details->quantity}}</span></p>
                                                        <label class="heading-five mb-0 order-product-price">{{@$setting->currency_symbol}}{{@$details->retail_price}}</label>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach 
                                        </ul>
                                    </li>   
                                        
                                </ul> 
                            </div>
                            <div class="col-lg-5 mt-30">
                                <div class="common-card product-tracking">
                                    <h5 class="mb-1">{{Helper::language('product_tracking')}}</h5>
                                    <span class="body-normal text-dark-grey"> {{Helper::language('tracking_id')}} #{{@$orderData->uniqid}}</span>
                                    <ul class="product-tracking-block">
                                        @foreach($ordertrackingTrackData as $order_date)
                                        <?php 
                                        //dd($order_date);
                                        $date = date('l, d F', strtotime($order_date));
                                        //$date = date("l jS \of F Y",strtotime($order_date));
                                        //dd($date);
                                        // $order_date = date("Y-m-d H:i:s",strtotime($order_date));
                                        // // echo "<pre>";print_r($order_date);exit();
                                        ?>
                                        
                                        <li>
                                            <span class="body-normal text-black text-bold">{{@$date}}</span>
                                        </li>
                                    
                                        @foreach($ordertrackingData as $orderdate)
                                        <?php 
                                        $track_date = date("h:i A",strtotime($orderdate->change_date));

                                        $order_status = Helper::order_status($orderdate->id);

                                        $orderdate = date("Y-m-d",strtotime($orderdate->change_date));
                                        ?>
                                        @if($orderdate == $order_date)
                                        <li>
                                            <span class="body-normal text-dark-grey">{{@$track_date}}</span>
                                            <div class="tracking-info">
                                                <span class="body-normal text-dark-grey d-block">{{@$order_status}}</span>
                                                
                                            </div>
                                        </li>
                                        @endif
                                        @endforeach
                                        
                                        @endforeach
                                       
                                    </ul>
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </section>
    </main>

@endsection