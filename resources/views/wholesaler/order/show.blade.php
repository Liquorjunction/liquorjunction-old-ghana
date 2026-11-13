@extends('wholesaler.layouts.master')
@section('title','Order | Wholesaler Panel')
@section('content')
    <link href="{{ asset("assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css") }}" rel="stylesheet">

    <link rel= "stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">
               
                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.view') }} Order Details
                </h3>
                <small>
                    <a href="{{ route('adminwholesalerHome') }}">{{ __('backend.home') }}</a> /
                   <a href="{{ url()->previous() }}">Order History</a> /
                   <span>{{ __('backend.view') }} Order Details</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ url()->previous() }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                <form class="table_design">
                <!-- <h2 class="text-center">Order Detail</h2> -->
                <?php 
                $method = DB::table('users_payments')->where('order_id',$orderData->id)->first();
                $order_type = @$orderData->order_type;
                $order_status = @$orderData->order_status;

                if ($method->payment_mode ==1) {
                    $payment_method = "Online";
                }else{
                    $payment_method = "Cash";
                }

                if ($order_status == 1) {
                    $order_status = "Dispatched";
                }elseif ($order_status==2) {
                    $order_status = "Completed";
                    
                }elseif ($order_status==3) {
                    $order_status = "Cancel";
                    
                }else{
                    $order_status = "Pending";

                }

                if ($order_type==1) {
                    $order_type = "Online";
                }else{
                    $order_type = "In Store";
                }
                ?>
                <table class="table">
                    <thead>
                        <tr>
                                          <td colspan="1"><p><b></b></p></td>
                                          <td colspan="1"><p><b></b></p></td>
                                          <td colspan="3"><h4><p><b>Customer Details</b></p></h4></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>Name</b></p></td>
                                          <td colspan="1"><p>{{@$orderData->first_name}} {{@$orderData->last_name}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>Email</b></p></td>
                                          <td colspan="1"><p>{{@$orderData->email}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>Phone</b></p></td>
                                          <td colspan="1"><p>{{@$orderData->phone}}</p></td>
                                        </tr>
                                        <?php $addr_data = \DB::table('user_address')->where('id',@$orderData->delivery_address_id)->first();?>
                                        <tr>
                                          <td colspan="1"><p><b></b></p></td>
                                          <td colspan="1"><p><b></b></p></td>
                                          <td colspan="3"><h4><p><b>Delivery Address Details</b></p></h4></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>Name</b></p></td>
                                          <td colspan="1"><p>{{@$addr_data->name}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>Phone</b></p></td>
                                          <td colspan="1"><p>{{@$addr_data->phone}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>Address</b></p></td>
                                          <td colspan="1"><p>{{@$addr_data->address}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>City</b></p></td>
                                          <td colspan="1"><p>{{@$addr_data->city}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>State</b></p></td>
                                          <td colspan="1"><p>{{@$addr_data->state}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>Country</b></p></td>
                                          <td colspan="1"><p>{{@$addr_data->country}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="1"><p><b></b></p></td>
                                          <td colspan="1"><p><b></b></p></td>
                                          <td colspan="3"><h4><p><b>Order Details</b></p></h4></td>
                                        </tr>
                                         <?php $trns_data = \DB::table('transactions')->where('id',@$orderData->transaction_id)->first();?>
                                          <td colspan="2"><p><b>Transaction ID</b></p></td>
                                          <td colspan="1"><p>{{@$trns_data->trans_no}}</p></td>
                                          <tr>
                                          <td colspan="2"><p><b>Order ID</b></p></td>
                                          <td colspan="1"><p>{{@$orderData->order_id}}</p></td>
                                          </tr>
                                          <tr>
                                          <td colspan="2"><p><b>Order Type</b></p></td>
                                          <td colspan="1"><p>{{@$order_type}}</p></td>
                                          </tr>
                                        </tr>
                                        <tr>
                                          <td colspan="2"><p><b>Order Status</b></p></td>
                                          <td colspan="1"><p>{{@$order_status}}</p></td>
                                        </tr>
                                         <tr>
                                          <td colspan="2"><p><b>Payment Method</b></p></td>
                                          <td colspan="1"><p>{{@$payment_method}}</p></td>
                                        </tr>
                                      </thead>
                                    <thead>
                                        <tr>
                                          <td colspan="1"><p><b></b></p></td>
                                          <td colspan="1"><p><b></b></p></td>
                                          <td colspan="3"><h4><p><b>Product Details</b></p></h4></td>
                                        </tr>
                                        <tr>
                                            <th><p></p></th>
                                            <th><p>Product Name</p></th>
                                            <th><p>Qty</p></th>
                                            <th><p>Amount</p></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orderDetails as $data)
                                        <tr>
                                            <td><p></p></td>
                                            <td><p>{{@$data->product_name}}</p></td>
                                            <td><p>{{@$data->quantity}}</p></td>
                                            <td><p>{{@$settings->currency_symbol}}{{@$data->retail_price}}</p></td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                          <td colspan="3"><p><b>Total Amount</b></p></td>
                                          <td colspan="1"><p>{{@$settings->currency_symbol}}{{@$orderData->total_amount}}</p></td>
                                        </tr>
                                        @php
                                        $discountPrice = $orderData->total_amount - $orderData->payable_amount;
                                        @endphp
                                        <tr>
                                          <td colspan="3"><p><b>Discount Amount</b></p></td>
                                          <td colspan="1"><p>{{@$settings->currency_symbol}}{{@$discountPrice}}</p></td>
                                        </tr>
                                        <tr>
                                          <td colspan="3"><p><b>Pay Amount</b></p></td>
                                          <td colspan="1"><p>{{@$settings->currency_symbol}}{{@$orderData->payable_amount}}</p></td>
                                        </tr>
                                        

                                      </tfoot>
                                </table>
               
                <div class="form-group row">
                    <div class="col-sm-2">
                        <a href="{{ url()->previous() }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                            <i class="material-icons">
                            &#xe5cd;</i> Cancel
                        </a>
                    </div>
                    <div class="col-sm-10">
                        
                    </div>
                </div>
            
            {{Form::close()}}
           
            </div>
        </div>
        
    </div>

   

@endsection

@push("after-scripts")
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> 
<script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>

 

    <script>
    


      

       
       

    </script>
@endpush
