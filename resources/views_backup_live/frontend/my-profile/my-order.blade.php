@extends('frontEnd.layouts.new_app')
@section('title','My Order')
@section('content')
@include('sweetalert::alert')

<main class="site-content">
    <div class="loader" id="loader"></div>
    <div class="bread-crumb-block">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                <li>
                    <p class="text-black body-normal">My Order</p>
                </li>
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
                    <div class="order-listing-header">
                        <h1 class="mb-0">My Order</h1>
                        <div class="sort-by mb-0">
                            <form action="">
                                <span class="heading-six mb-0">Sort by :</span>
                                <select name="sort_by" id="sort_by">
                                    <option value="0">All</option>
                                    <option value="1">Purchase Online</option>
                                    <option value="3">Purchase In Store</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="orderFilterData">
                        @if($orderData->count() > 0)
                        @foreach($orderData as $order)

                        <?php
                        // dd($order);
                        $order_palce_date = \Helper::converttimeTozone($order->created_at);
                        $order_palce_date = date("d F Y", strtotime($order_palce_date));

                        $productData = DB::table('order_detail')->leftjoin('product', 'product.id', '=', 'order_detail.product_id')->leftJoin('main_users', 'main_users.id', '=', 'order_detail.supplier_id')->select('order_detail.*', 'product.product_name', 'product.product_image', 'main_users.store_name', 'main_users.id as store_id')->where('order_detail.order_id', $order->id)->first();
                        // dd($productData);
                        // echo "<pre>";print_r($order->id);exit();

                        $productDataMore = DB::table('order_detail')->where('order_id', $order->id)->count();

                        $productDataMore = $productDataMore - 1;


                        $order_type = "";

                        if ($order->order_type == 1) {
                            $order_type = "Purchase Online";
                        } else {
                            $order_type = "Purchase Offline";
                        }
                        ?>
                        @if($order->order_type == 3)
                        <div class="common-card recent-order order-list ">
                            <ul class="order-detail">
                                <li>
                                    <span class="body-normal text-dark-grey d-block mb-1">Order Placed</span>
                                    <span class="body-normal text-dark-grey d-block mb-0">{{@$order_palce_date}}</span>
                                </li>
                                <li>

                                </li>
                                <li class="flex-lg-fill">

                                </li>
                                <li>
                                    <span class="body-normal text-dark-grey d-block mb-1">Order ID #{{@$order->order_id}}</span>

                                </li>
                            </ul>

                            <h6>There is not items in this order yet.</h6>

                            <div class="mini-product-top">

                            </div>
                        </div>
                        @else
                        <div class="common-card recent-order order-list ">
                            <ul class="order-detail">
                                <li>
                                    <span class="body-normal text-dark-grey d-block mb-1">Order Placed</span>
                                    <span class="body-normal text-dark-grey d-block mb-0">{{@$order_palce_date}}</span>
                                </li>
                                <li>
                                    <span class="body-normal text-dark-grey d-block mb-1">Order Type</span>
                                    <span class="body-normal text-dark-grey d-block mb-0">{{@$order_type}}</span>
                                </li>
                                <li class="flex-lg-fill">
                                    <span class="body-normal text-dark-grey d-block mb-1">Deliver To</span>
                                    <span class="body-normal text-dark-grey d-block mb-0">{{@$order->ship_to}}</span>
                                </li>
                                <li>
                                    <span class="body-normal text-dark-grey d-block mb-1">Order ID #{{@$order->order_id}}</span>
                                    <a href="{{route('order-detail',['id'=>$order->id])}}" class="red-text-link body-normal text-uppercase mb-0">View order details</a>
                                </li>
                            </ul>
                            @if($order->order_type==1)
                            <h6>Online Store Order @if($productDataMore > 0)
                                <span class="text-dark-grey">+ {{@$productDataMore}} More Products</span>
                            </h6>
                            @endif</h6>
                            @else
                            <h6>Pick-up In Store Order @if($productDataMore > 0) <span class="text-dark-grey">+ {{@$productDataMore}} More Products</span>@endif</h6>
                            @endif
                            <div class="mini-product-top">
                                <div class="product-img">
                                    <a href="{{route('productdetails',['id'=>$productData->product_id])}}">
                                        <img src="{{ asset('uploads/product/').'/'.$productData->product_image }}" alt="product img">
                                    </a>
                                </div>

                                <div class="product-detail">
                                    <a href="{{route('productdetails',['id'=>$productData->product_id])}}" class="heading-six mb-0">{{@$productData->product_name}}</a>
                                    <span class="d-block mb-1">Sold by : <a href="{{route('productlistview',['id'=>$productData->store_id])}}">{{@$productData->store_name}}</a></span>
                                    <label class="body-normal text-dark-grey d-block mb-1"></label>
                                    <p class="body-normal text-black mb-0">Quantity : <span class="body-normal text-dark-grey mb-0 ms-1">{{@$productData->quantity}}</span></p>
                                    <label class="heading-four mb-0 order-product-price">{{@$setting->currency_symbol}}{{@$order->payable_amount}}</label>
                                    <a href="#" onclick="return Reorder({{$order->id}})" class="reorder-btn small-border-btn hvr-radial-out-border">reorder</a>
                                </div>


                            </div>
                        </div>
                        @endif

                        @endforeach
                        @else
                        <div class="col-lg-12 col-sm-12">
                            <h3 class="text-danger text-center">No data found</h3>
                        </div>
                        @endif
                        {{ $orderData->links('vendor.pagination.custom_pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script type="text/javascript">
    $(document).on('change', '#sort_by', function(e) {
        // alert('hello')
        e.preventDefault();
        var sort_by = $("#sort_by").val();

        $(document).on('click', '.pagination li a', function(e) {
            e.preventDefault();
            var sort_by = $("#sort_by").val();

            // alert('jjkk')
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            var url = $(this).attr('href');
            var page = url.split('page=')[1];
            // sorting_all(query,option,multiadd,level,min_dance_class_price,max_dance_class_price,min_duration, max_duration,page);
            OrderFilter(sort_by, page);
        });
        OrderFilter(sort_by, 1);
    });

    function OrderFilter(sort_by, page) {
        // alert(sort_by);
        action_url = "{{ route('order-filter') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
            url: action_url,
            data: {
                'sort_by': sort_by,
                'page': page
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
                $(document).find(".orderFilterData").empty();

                $(document).find(".orderFilterData").append(response.html);
                $('.loader').css("visibility", "hidden");
            },
        });
    }

    function Reorder(id) {
        // alert(id)
        action_url = "{{ route('re-order') }}";
        var csrf = "{{ csrf_token() }}";

        $.ajax({
            url: action_url,
            data: {
                'id': id
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
                if (response.code != 0) {
                    $('.loader').css("visibility", "visible");
                    var url = "{{route('cart')}}";
                    window.location.href = url;
                } else {
                    location.reload();
                }
            },
        });
    }
</script>
@endsection