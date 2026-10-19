@extends('frontEnd.layouts.new_app')
@section('title','Product List')
@section('content')
@include('sweetalert::alert')
<?php 
$category_id = Session::get('category_id');
$price_filter = Session::get('price_filter');
$most_view = Session::get('most_view');
// $most_view = 0;
// echo "test:<pre>";print_r($category_id);exit();

?>
<main class="site-content">
    <div class="loader" id="loader"></div> 
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    
                    <li><a href="{{route('store.listing-online')}}" class="text-grey body-normal">store</a></li>
                    
                    <li><p class="text-black body-normal">{{@$store_data->store_name}}</p></li>
                </ul>
            </div>
        </div>
        <!-- <section class="store-discription-block pt-30">
            <div class="container">
                <div class="store-discription">
                    <div class="store-left">
                        <span class="d-block store-image">
                            @if (@$store_data->profile)
                            
                                <img src="{{ asset('uploads/customer/').'/'.$store_data->profile }}" alt="Store Image" height="100" width="100">
                            
                            @else
                                <img src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="Store Image" height="100" width="100">
                            @endif
                            
                        </span>
                        <h2 class="mb-0">{{@$store_data->store_name}}</h2>
                        <input type="hidden" name="supplier_id" id="supplier_id" value="{{@$store_data->id}}">
                        <input type="hidden" name="purchase" id="purchase" value="{{@$purchase}}">
                    </div>
                    <div class="store-right">
                        <p class="body-large text-dark-grey">{{@$store_data->store_description}}.</p>
                    </div>
                </div>
            </div>
        </section> -->
        <section class="store-discription-block pt-30">
            <div class="container">
                <div class="store-discription">
                    <div class="store-left">
                        <span class="d-block store-image">
                            @if (@$store_data->profile)
                            
                                <img src="{{ asset('uploads/customer/').'/'.$store_data->profile }}" alt="Store Image" height="100" width="100">
                            
                            @else
                                <img src="{{ asset('assets/dashboard/images/no_image_found.jpg')}}" alt="Store Image" height="100" width="100">
                            @endif
                        </span>
                        <div class="store-detail">
                            <h2 class="mb-1">{{@$store_data->store_name}}</h2>
                            <p class="body-normal"></p>
                            <input type="hidden" name="supplier_id" id="supplier_id" value="{{@$store_data->id}}">
                        <input type="hidden" name="purchase" id="purchase" value="{{@$purchase}}">
                        </div>                        
                    </div>
                    <div class="store-right">
                        <p class="body-large text-dark-grey">{{@$store_data->store_description}}</p>
                         <button type="submit" class="small-border-btn hvr-radial-out" id="purchase_order">Create Purchase Order</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="product-listing pt-30 py-80">
            <div class="container">
                <div class="row" id="">
                    <div class="col-lg-3 col-md-4">
                        <div class="product-filter">
                            <form class="d-flex align-items-center justify-content-between">
                                <label class="body-extra-large mb-0">Filter</label>
                                <!-- <button class="clear-all-btn">Clear all</button> -->
                            </form>
                        </div>
                        <div class="product-filter-block" id="sidebar_product_filter">
                            <div class="filter-content">
                                <div class="check-group">
                                    @if($most_view == 0)
                                    <input class="form-check-input" type="checkbox" value="0" onclick="return mostViewed(1);" id="flexCheckDefault26">
                                    <label class="form-check-label" for="flexCheckDefault26">Most viewed</label>
                                    @else
                                    <input class="form-check-input" type="checkbox" value="1" onclick="return mostViewedRemove(0);" id="flexCheckDefault26" checked>
                                    <label class="form-check-label" for="flexCheckDefault26">Most viewed</label>
                                    @endif
                                   
                                </div>
                            </div>
                            <div class="filter-content">
                                <h6>Categories</h6>
                                <ul class="mb-0">
                                    <li>
                                        <div class="radio-group">
                                            <input class="test" type="radio" name="common-radio213" id="radioDefault123" value="all"  <?php if ($category_id == "all") { ?>
                                                checked
                                            <?php }elseif ($category_id == "") { ?>
                                                checked
                                            <?php } ?>   >
                                            <label for="radioDefault123">All</label>
                                        </div>
                                    </li>
                                    @foreach($categoryData as $key=>$category)
                                        <li>
                                        <div class="radio-group">
                                            <input id="radioDefault{{$key}}" {{ ($category_id == $category->id) ? 'checked' : "" }} class="test" name="category_id" value="{{@$category->id}}" type="radio">
                                            <label for="radioDefault{{$key}}">{{@$category->title}}</label>
                                        </div>
                                    </li>
                                    @endforeach
                                    
                                </ul>                                
                            </div>
                           
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8" id="filterData">
                        <input type="hidden" name="user_id" id="user_id" value="{{@$user_id}}">
                        @if(!empty($user_id))
                        <div class="sort-by">
                            <form action="">
                                <select name="price_filter" id="price_filter" class="price_filter"  onclick="return PriceFilter(this)">
                                <span class="heading-six mb-0">Sort by :</span>
                                    <option {{ ($price_filter == 1) ? 'selected' : "" }} value="1">Price: High to Low</option>
                                    <option {{ ($price_filter == 2) ? 'selected' : "" }} value="2">Price: Low to High</option>
                                </select>
                            </form>
                        </div>
                        @endif
                        <div class="row product-listing-row">
                            @if($product_list->count() > 0)
                                @foreach($product_list as $key2=>$product)
                                <?php
                                $user_id = @$user_id; 
                                $fav_data = DB::table('favorite_product')->where('user_id',$user_id)->where('product_id',$product->id)->where('status',1)->first();
                                // echo "<pre>";print_r($fav_data);exit();
                                ?>
                                <input type="hidden" name="category_id" id="category_id" value="{{@$category_id}}">
                                    <div class="col-lg-4 col-sm-6 day-n-time-parent product-listing-col">
                                <a href="{{route('productdetails',['id'=>$product->id])}}" class="product-box">
                                
                                    <div class="img-box">
                                        <!-- <img src="../../assets/frontend/images/product_solar_panel.png" alt="solar panel"/> -->
                                <img src="{{ asset('uploads/product/').'/'.$product->product_image }}" alt="Category Image">
                                    </div>
                                    <div class="detail-box">
                                        <h5 class="mb-2">{{@$product->product_name}}</h5>
                                        <p>{{@$product->short_description}}</p>
                                         @if(!empty($user_id))
                                         @if($product->discount_price != 0)
                                         <h6 class="mb-0 text-black">{{@$setting->currency_symbol}}{{ isset($product->discount_price) ? $product->discount_price : $product->retail_price}}<span class="d-inline-block body-normal disable-price">{{@$setting->currency_symbol}}{{@$product->retail_price}}</span></h6>
                                          @else
                                          <h6 class="mb-0 text-black">{{@$setting->currency_symbol}}{{$product->retail_price}}<span class="d-inline-block body-normal disable-price">{{@$setting->currency_symbol}}{{@$product->retail_price}}</span></h6>
                                          @endif
                                         @else
                                         <!-- <h6 class="mb-0 text-red">Login For Price</h6> -->
                                         <?php $url = $_SERVER['REQUEST_URI'];
                                                $new_url = explode('/',$url);
                                         ?>
                                         <button class="text-red small-common-btn hvr-radial-out" onclick="return LoginForPrice({{$new_url[2]}})">Login For Price</button>
                                         @endif
                                    </div>
                                   
                                    <input class="fav-icon checked_box" type="checkbox" id="fav-item{{$key2}}" value="{{ ($fav_data != "") ? '1' : "0" }}" {{ ($fav_data != "") ? 'checked' : "" }} onclick="return productFav({{$product->id}},{{ ($fav_data != "") ? '1' : "0" }})" />
                                    <label class="fav-button" for="fav-item{{$key2}}"></label>
                                   
                        
                                </a>
                            </div>
                                @endforeach
                            @else
                            
                            <div class="text-center text-bold">
                                <h3>No Product Found</h3>
                            </div>
                            @endif
                            
                        </div>
                        {{ $product_list->links('vendor.pagination.custom_pagination') }}
                    </div>
                </div>
            </div>
            <div class="bottom_button">
                <ul class="mb-0">
                    <li>
                        <button type="button" class="filter" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
                    </li>
                    <li class="last-child">
                        <button type="button" class="sort" data-bs-toggle="modal" data-bs-target="#sortModal">Sort</button>
                    </li>
                </ul>
            </div>
        </section>
    </main>
    <div class="modal filter-modal fade show p-0" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-filter">
        <div class="modal-content">
            <div class="modal-header">
                <span class="body-extra-large mb-0">Filter</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark text-black"></i></button>
            </div>
            <div class="modal-body">
                <div class="product-filter-block">
                    <div class="filter-content">
                        <div class="check-group">
                            <!-- <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
                            <label class="form-check-label" for="flexCheckDefault">Most viewed</label> -->
                             @if($most_view == 0)
                                    <input class="form-check-input" type="checkbox" value="0" onclick="return mostViewedresponse(1);" id="flexCheckDefault26">
                                    <label class="form-check-label" for="flexCheckDefault27">Most viewed</label>
                                    @else
                                    <input class="form-check-input" type="checkbox" value="1" onclick="return mostViewedRemoveresponse(0);" id="flexCheckDefault26" checked>
                                    <label class="form-check-label" for="flexCheckDefault27">Most viewed</label>
                                    @endif
                        </div>
                    </div>
                    <div class="filter-content">
                        <h6>Categories</h6>
                        <ul class="mb-0">
                            <li>
                                <div class="radio-group">
                                    <input id="radioDefault" value="all"   <?php if ($category_id == "all") { ?>
                                                checked
                                            <?php }elseif ($category_id == "") { ?>
                                                checked
                                            <?php } ?> onclick="return CategoryFilterResponsive('all')" type="radio" name="common-radio">
                                    <label for="radioDefault" onclick="return CategoryFilterResponsive('all')">All</label>
                                </div>
                            </li>
                            @foreach($categoryData as $key=>$category)
                            <li>
                                <div class="radio-group">
                                    <input id="radioDefault{{$key}}"  {{ ($category_id == $category->id) ? 'checked' : "" }} value="{{@$category->id}}" onclick="return CategoryFilterResponsive({{@$category->id}})"  type="radio" name="common-radio">
                                    <label for="radioDefault{{$key}}" onclick="return CategoryFilterResponsive({{@$category->id}})">{{@$category->title}}</label>
                                </div>
                            </li>
                            @endforeach
                        </ul>                                
                    </div>
                   
                </div>
            </div>
           <!--  <div class="modal-footer">
                <button type="button" class="common-btn bg-dark hvr-radial-out" data-bs-dismiss="modal">Reset</button>
                <button type="button" class="common-btn hvr-radial-out">Apply</button>
            </div> -->
        </div>
    </div>
</div>

<div class="modal sort-modal fade show p-0" id="sortModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sort">
        <div class="modal-content">
            <div class="modal-header">
                <span class="body-extra-large mb-0">Sort By</span>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark text-black"></i></button> -->
            </div>
            <div class="modal-body">
                <div class="product-filter-block">                    
                    <div class="filter-content">
                        <ul class="mb-0">
                            <li>
                                <div class="radio-group">
                                    <input id="high_to_low" value="1" onclick="return priceFilterResponse(1);" type="radio" name="common-radio">
                                    <label class="{{ ($price_filter == 1) ? 'active' : "" }}" for="high_to_low" onclick="return priceFilterResponse(1);">Price: High to Low</label>
                                </div>
                            </li>
                            <li>
                                <div class="radio-group">
                                    <input id="low_to_high" value="2" onclick="return priceFilterResponse(2);" type="radio" name="common-radio">
                                    <label class="{{ ($price_filter == 2) ? 'active' : "" }}" for="low_to_high" onclick="return priceFilterResponse(2);">Price: Low to High</label>
                                </div>                                
                            </li>                            
                        </ul>                                
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal order-successfully-modal fade p-0" id="OrderSuccessfully" tabindex="-1" aria-modal="true" role="dialog">
            

            
        </div>
<div class="modal fade remove-item-modal p-0 show" id="confirmModal" tabindex="-1" aria-modal="true">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    <input type="hidden" name="address_id" id="address_id" value="">
     <div class="modal-body">
        <h5 class="mb-3">Purchase Order</h5>
         <p class="body-large mb-4">Are you sure you want to create purchase order ?</p>
          <div class="d-flex justify-content-between gap-2">
           <button type="submit" class="small-common-btn common-border-btn hvr-radial-out-black w-100" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            <button type="submit" class="small-common-btn hvr-radial-out w-100" onclick="return createPurchaseOrder()" id="yes_btn">Yes</button>
             </div>
              </div>
               </div>
                </div>
            </div>
        </div>        
    <script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script type="text/javascript">

    $('#purchase_order').click(function() {
       $('#confirmModal').modal('show');
    });

    $('#yes_btn').click(function() {
       $('#confirmModal').modal('hide');
    });

    function LoginForPrice(product_id_list)
    {
        event.preventDefault();
        localStorage.setItem('product_id_list',product_id_list);
        var url = "{{route('websitelogin')}}";
        window.location.href = url;
    }

    function createPurchaseOrder()
    {
        // alert()
        var supplier_id = $("#supplier_id").val();
        // alert(supplier_id)
        action_url = "{{ route('purchaseorder') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'supplier_id':supplier_id},
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
                                // $('.loader').css("visibility", "hidden");
                                // location.reload();
                                $('.loader').css("visibility", "hidden");
                                // $("#OrderSuccessfully").html("");
                                $(document).find("#OrderSuccessfully").html(response.html);
                                $('#OrderSuccessfully').modal('show');
                                // $("#OrderSuccessfully").html("");
                                // console.log('test'+response);
                                   
                                     // $(document).find("#filterData").modal('show');
                            },
                        });
    }


    function mostViewed(id){

         var price_filter = $("#price_filter").val();
        var category_id = $("#category_id").val();
        var most_view = id;
        CategoryFilter(category_id,'1',price_filter,most_view);
    }

    function mostViewedRemove(id){

         var price_filter = $("#price_filter").val();
        var category_id = $("#category_id").val();
        var most_view = id;
        CategoryFilter(category_id,'1',price_filter,most_view);
    }

    function mostViewedresponse(id) {
         
        
        action_url = "{{ route('productresponsemostview') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'most_view':id},
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
                                $('.loader').css("visibility", "hidden");
                                location.reload();
                                // console.log(response);
                                // return false;
                                // console.log('test'+response);
                                   
                                     // $(document).find("#filterData").modal('show');
                            },
                        });
    }

    function mostViewedRemoveresponse(id) {
        
        action_url = "{{ route('productresponsemostviewremove') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'most_view':id},
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
                                $('.loader').css("visibility", "hidden");
                                location.reload();
                                // console.log(response);
                                // return false;
                                // console.log('test'+response);
                                   
                                     // $(document).find("#filterData").modal('show');
                            },
                        });
    }

    function priceFilterResponse(id){
        // e.preventDefault()
        // alert(id);
        var supplier_id = $("#supplier_id").val();
        var price_filter = id;
        action_url = "{{ route('productresponsepricefilter') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'price_filter':price_filter,'supplier_id':supplier_id},
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
                                $('.loader').css("visibility", "hidden");
                                location.reload();
                                // console.log(response);
                                // return false;
                                // console.log('test'+response);
                                   
                                     // $(document).find("#filterData").modal('show');
                            },
                        });
    }

    function CategoryFilterResponsive(id) {
        // alert(id)
        var category_id = id;
        var supplier_id = $("#supplier_id").val();

        action_url = "{{ route('productresponsefilter') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
                            url: action_url,
                            data: {'category_id':category_id,'supplier_id':supplier_id},
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
                                $('.loader').css("visibility", "hidden");
                                location.reload();
                                // console.log(response);
                                // return false;
                                // console.log('test'+response);
                                   
                                     // $(document).find("#filterData").modal('show');
                            },
                        });
    }
    function CategoryFilter(id,page,price_filter,most_view){
        // alert('Page'+most_view)
        // return false;
        var category_id = id;
        var price_filter = $("#price_filter").val();

        var supplier_id = $("#supplier_id").val();
        var most_view = most_view;
        var purchase = $("#purchase").val();
        // var category_id = $("#category_id").val();
        // var category_id = $("#category_id").val();
        // alert('price_filter'+price_filter)
        action_url = "{{ route('productfilter') }}";
        var csrf = "{{ csrf_token() }}";
        $.ajax({
                            url: action_url,
                            data: {'category_id':category_id,'supplier_id':supplier_id,'price_filter':price_filter,'page':page,'purchase':purchase,'most_view':most_view},
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
                                // console.log(response);
                                // return false;
                                // console.log('test'+response);
                                    $('.loader').css("visibility", "hidden");

                                   $(document).find("#filterData").empty();
                                   $(document).find("#sidebar_product_filter").empty();
                                    $(document).find("#filterData").append(response.html);
                                    $(document).find("#sidebar_product_filter").append(response.html1);
                                     // $(document).find("#filterData").modal('show');
                            },
                        });
    }


    $(document).on('change', '.test', function(e){
        // alert()
e.preventDefault();
    var category_id = $(this).val();
    // alert('category'+category_id)
    $(document).on('click', '.pagination li a', function(e){
e.preventDefault();
var price_filter = $("#price_filter").val();
        var most_view = $("#flexCheckDefault26").val();
// alert('jjkk')
 $('li').removeClass('active');
 $(this).parent('li').addClass('active');
 var url = $(this).attr('href');
var page = url.split('page=')[1];
 // sorting_all(query,option,multiadd,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration,page);
    CategoryFilter(category_id,page,price_filter,most_view);
 });
    var price_filter = $("#price_filter").val();
        var most_view = $("#flexCheckDefault26").val();
    CategoryFilter(category_id,1,price_filter,most_view);
    // alert(category_id)
 });

    
    function PriceFilter(){
       
        var price_filter = $("#price_filter").val();
        var category_id = $("#category_id").val();
        var most_view = $("#flexCheckDefault26").val();
        CategoryFilter(category_id,'1',price_filter,most_view);
        // alert(price_filter)
    }


    function productFav(product_id,status) {
      
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