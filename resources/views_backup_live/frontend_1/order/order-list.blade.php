@extends('frontend.layouts.app')
@section('title','Order Listing')
@section('content')
@include('sweetalert::alert')


<div class="bread-crumb-block">
    <div class="container">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{Helper::language('my_order')}}</li>
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
                <div class="order-listing-header">
                    <h2 class="mb-0">{{Helper::language('my_orders')}}</h2>                            
                </div>   
                @if(isset($orderInfo) && count($orderInfo) > 0)                    
                @foreach ($orderInfo as $result )  
                @php
                    $order_details = $result->order_details->first();
                    $unit = Helper::getUnitById($order_details->variant_unit);
                    $product_info = Helper::getProductDetails($order_details->product_id);
                        $product_image = $product_info->get_product_images->first();
                        $product_title='';
                        if(session::get('language')==2){                            
                            $product_title = $product_info->product_name_fr;
                        }else{
                            $product_title = $product_info->product_name;
                        } 
                      
                        $orderDate = $result->order_date;
                        $newDate = date("d-m-Y", strtotime($orderDate));
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $newDate);
                        $daysToAdd = Helper::Settings('delivery_days');
                        $date1 = $date->addDays($daysToAdd);
                        $newDate1 = date("d F Y", strtotime($date1));
                @endphp
                <div class="common-card recent-order order-list">
                    <ul class="order-detail">
                        <li>
                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('order_placed')}}</span>
                            <span class="body-normal text-dark-grey d-block mb-0">
                            <?php
                                // $time = strtotime(@$result->created_at);
                                // $dateInLocal = date("Y-m-d H:i:s", $time);
                            ?>
                             @php
                                $order_placed = \Helper::converttimeTozone($result->created_at);
                                echo $order_palce_date = date("d M Y",strtotime($order_placed));
                             @endphp
                               
                            </span> 
                        </li>
                        @if($result->order_type==1)
                        <li>
                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('deliver_to')}}</span>
                            <span class="body-normal text-dark-grey d-block mb-0">
                            @php
                                $order_customer_name  = explode(',|', $result->delivery_address);
                                echo $order_customer_name  = $order_customer_name[0];
                                //$order_customer_name  =  $result->orderInfo->customer_name;
                                //echo $order_customer_name  = $order_customer_name;
                            @endphp

                            </span>
                        </li>
                        @endif
                        @if($result->order_type==2)
                        <li>
                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('order_pickup_from')}}</span>
                            <span class="body-normal text-dark-grey d-block mb-0">
                                @php
                                   // $pickup  = explode(', ', $result->orderInfo->store_pickup_address);
                                    //echo $pickup[0];
                                    if ( strpos($result->orderInfo->store_pickup_address, ',|' ) !== false ) {
                                        $pickup  = explode(',| ', $result->orderInfo->store_pickup_address);   
                                        $name = array_shift($pickup);  
                                        echo ($name)?$name:'';  
                                    };
                                    
                                   
                                @endphp        
                            </span>
                        </li>
                        @endif
                        
                        <li>
                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('order_type')}}</span>
                            <span class="body-normal text-dark-grey d-block mb-0">
                            @if($result->order_type==1)
                                {{'Online'}}
                            @elseif($result->order_type==2)
                                {{'Pickup Order'}}
                            @endif
                            </span>
                        </li>
                        <li class="flex-sm-fill">
                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('order_status')}}</span>
                            <span class="body-normal text-dark-grey d-block mb-0">{{Helper::getOrderStatus($result->order_status)}}</span>
                        </li>
                        <li>
                            <span class="body-normal text-dark-grey d-block mb-1">{{Helper::language('order')}} #{{@$result->order_id}}</span>
                            <a href="{{route('order-detail',['id'=>Helper::encodeUrl($result->id)])}}" class="border-button mb-0">{{Helper::language('view_order_details')}}</a>
                        </li>
                    </ul>
                    @if($result->order_type==1)
                    <h6 class="order-list-title">
                        {{Helper::language('order_delivery_on')}} &nbsp;<span class="d-inline-block text-yellow">{{$newDate1}}</span> 
                        <span class="text-dark-grey text-grey-dark">    
                       {{(count($result->order_details)!=1)?'+'.(count($result->order_details) - 1).' More Products':''}}
                        </span>
                    </h6>    
                    @endif                        
                    <div class="single-product">
                        <div class="product-img">                                            
                            <a href="{{route('productdetails',['id'=>Helper::encodeUrl($order_details->product_id)])}}">                                 
                                @if (file_exists(public_path() . '/uploads/product/'.$product_image->image))					
                                <img src="{{ asset('uploads/product/'.$product_image->image) }}" title="{{$product_title}}" />
                                @else
                                <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                                @endif 
                            </a>
                        </div>
                        <div class="product-detail">
                            <div class="product-detail-main">
                                <h6><a href="{{route('productdetails',['id'=>Helper::encodeUrl($order_details->product_id)])}}" class="heading-six mb-0">{{ucfirst($product_title)}}</a></h6>
                                <p class="quantity">{{Helper::language('volume')}}<span>: {{$order_details->variant_size.' '.$unit}}</span></p>
                                <p class="quantity">{{Helper::language('quantity')}}<span>: {{$order_details->quantity}}</span></p>
                            </div>
                            <ul>
                                <li class="product-pricing">
                                    @if($order_details->product_total_amount!=0)
                                    <h5>
                                        {{$order_details->product_total_amount.Helper::Settings( 'currency_symbol')}}
                                        <span>
                                            {{$order_details->product_original_amount.Helper::Settings( 'currency_symbol')}}
                                        </span>
                                    </h5>
                                    @else
                                    <h5>
                                         {{$order_details->product_original_amount.Helper::Settings( 'currency_symbol')}}
                                    </h5>

                                    @endif
                                </li>                                        
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center"><h4 style="color: red;">{{Helper::language('no_result_found')}}</h4></div>
                @endif
                
                
            </div>
        </div>
    </div>
</section>
@endsection