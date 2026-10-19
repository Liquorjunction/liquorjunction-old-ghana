@extends('frontEnd.layouts.new_app')
@section('title','Product List')
@section('content')
@include('sweetalert::alert')

 <main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">                    
                    <li><p class="text-grey body-normal fw-light">Result for<span class="text-black body-normal text-bold ms-1">"{{@$category_name->title}}"</span></p></li>
                </ul>
            </div>
        </div>        
        <section class="product-listing search-history pt-30 py-80">
            <div class="container">
                <h1><span class="d-inline-block">Product List</span></h1>
                <div class="row product-listing-row">
                	<input type="hidden" name="user_id" id="user_id" value="{{@$user_id}}">
                    @if($category_data->count() > 0)
                	@foreach($category_data as $key2=>$product)
                	<?php
                                $user_id = @$user_id; 
                                $fav_data = DB::table('favorite_product')->where('user_id',$user_id)->where('product_id',$product->id)->where('status',1)->first();
                                // echo "<pre>";print_r($fav_data);exit();
                                ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 product-listing-col">
                        <a href="{{route('productdetails',['id'=>$product->id])}}" class="product-box">
                            <div class="img-box">
                                <img src="{{ asset('uploads/product/').'/'.$product->product_image }}" alt="flooring"/>
                            </div>
                            <div class="detail-box">
                                        <h5 class="mb-2">{{@$product->product_name}}</h5>
                                        <p>{{@$product->short_description}}</p>
                                         @if(!empty($user_id))
                                         <h6 class="mb-0 text-black">{{@$setting->currency_symbol}}{{ isset($product->discount_price) ? $product->discount_price : $product->retail_price}}<span class="d-inline-block body-normal disable-price">{{@$setting->currency_symbol}}{{@$product->retail_price}}</span></h6>
                                         @else
                                         <!-- <h6 class="mb-0 text-red">Login For Price</h6> -->
                                         <button class="text-red small-common-btn hvr-radial-out">Login For Price</button>
                                         @endif
                                    </div>
                            <input class="fav-icon checked_box" type="checkbox" id="fav-item{{$key2}}" value="{{ ($fav_data != "") ? '1' : "0" }}" {{ ($fav_data != "") ? 'checked' : "" }} onclick="return productFav({{$product->id}},{{ ($fav_data != "") ? '1' : "0" }})" />
                                    <label class="fav-button" for="fav-item{{$key2}}"></label>
                        </a>
                    </div>
                    @endforeach
                     @else
                        <div class="col-lg-12 col-sm-12">
                            <h3 class="text-danger text-center">No data found</h3>
                        </div>
                        @endif                    
                </div>
                 {{ $category_data->links('vendor.pagination.custom_pagination') }}
                
            </div>            
        </section>
    </main>
 <script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
    <script type="text/javascript">
    	function productFav(product_id,status) {
      	// alert()
        var status = status;
        var user_id = $("#user_id").val();
        // alert(user_id);
        if (user_id) {
            // alert('ttt')
        action_url = "{{ route('productfav') }}";
        }else{
            // alert('ttt25')
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
                                $('.loader').css("visibility", "visible");
                                // return false;
                                location.reload();
                            },
                        });

    }
    </script>
@endsection