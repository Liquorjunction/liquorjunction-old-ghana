@if($orderData->count() > 0)                       
                        @foreach($orderData as $order)

                        <?php

                         $order_palce_date = \Helper::converttimeTozone($order->created_at);
                         $order_palce_date = date("d F Y",strtotime($order_palce_date));
                            
                            $productData = DB::table('order_detail')->leftjoin('product','product.id','=','order_detail.product_id')->leftJoin('main_users','main_users.id','=','order_detail.supplier_id')->select('order_detail.*','product.product_name','product.product_image','main_users.store_name')->where('order_id',$order->id)->first();

                             $productDataMore = DB::table('order_detail')->where('order_id',$order->id)->count();

                            $productDataMore = $productDataMore - 1;

                            // echo "<pre>";print_r($productData);exit();

                            $order_type = "";

                            if ($order->order_type == 1) {
                                $order_type = "Purchase Online";
                            }else{
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
                                    <span class="body-normal text-dark-grey d-block mb-1">Order #{{@$order->order_id}}</span>
                                   
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
                                    <span class="body-normal text-dark-grey d-block mb-1">Order #{{@$order->order_id}}</span>
                                    <a href="{{route('order-detail',['id'=>$order->id])}}" class="red-text-link body-normal text-uppercase mb-0">View order details</a>
                                </li>
                            </ul>
                            @if($order->order_type==1)
                            <h6>Online Store Order  @if($productDataMore > 0)
                                <span class="text-dark-grey">+ {{@$productDataMore}} More Products</span></h6>
                                @endif</h6>
                            @else
                            <h6>Pick-up In Store Order  @if($productDataMore > 0) <span class="text-dark-grey">+ {{@$productDataMore}} More Products</span>@endif</h6>
                            @endif
                            <div class="mini-product-top">
                                <div class="product-img">
                                    <a href="{{route('productdetails',['id'=>$productData->product_id])}}">
                                        <img src="{{ asset('uploads/product/').'/'.$productData->product_image }}" alt="product img">
                                    </a>
                                </div> 
                                
                                <div class="product-detail">
                                    <a href="#" class="heading-six mb-0">{{@$productData->product_name}}</a>
                                    <span class="d-block mb-1">Sold by : <a href="#">{{@$productData->store_name}}</a></span>
                                    <label class="body-normal text-dark-grey d-block mb-1"></label>
                                    <p class="body-normal text-black mb-0">Quantity : <span class="body-normal text-dark-grey mb-0 ms-1">{{@$productData->quantity}}</span></p>
                                    <label class="heading-four mb-0 order-product-price">{{@$setting->currency_symbol}}{{@$order->payable_amount}}</label>
                                    <a href="#" onclick="return Reorder({{$order->id}})" class="reorder-btn small-border-btn hvr-radial-out-border">reorder</a>
                                </div>
                                                              
                                
                            </div>                      
                        </div>
                        @endif
                        
                        @endforeach
                       {{ $orderData->links('vendor.pagination.custom_pagination') }}
                        @else
                        <div class="col-lg-12 col-sm-12">
                            <h3 class="text-danger text-center">No data found</h3>
                        </div>
                        @endif