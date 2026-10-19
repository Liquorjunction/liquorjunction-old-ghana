@extends('dashboard.layouts.master')
@section('title', 'Order | Admin Panel')
@section('content')
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->


    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <style>
        .remove-border {
            border-top: none !important;
        }
    </style>
    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">

                <h3><i class="material-icons">
                        &#xe02e;</i> {{ __('backend.view') }} Order Details
                </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('adminorder') }}">Order Managment</a> /
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
                    
                    $method = DB::table('transactions')
                        ->where('order_id', $orderData->id)
                        ->first();
                    $order_type = @$orderData->order_type;
                    // dd(@$orderData);
                    $order_status = @$orderData->order_status;
                    // if (!empty($method) && $method->payment_type == 1) {
                    //     $payment_method = 'Momo Pay';
                    // } elseif{
                    //     $payment_method = 'Card';
                    // }
                    
                    if ($orderData->payment_type == 1) {
                        $payment_type = 'Online Payment';
                    } elseif ($orderData->payment_type == 3) {
                        $payment_type = 'Cash On Delivery';
                    } else {
                        $payment_type = 'Online Payment';
                    }
                    if ($orderData->order_status == 1) {
                        $order_status = 'Pending';
                    } elseif ($orderData->order_status == 2) {
                        $order_status = 'Accepted';
                    } elseif ($orderData->order_status == 3) {
                        $order_status = 'Delivered';
                    } elseif ($orderData->order_status == 5) {
                        $order_status = 'Ready to Pick Up';
                    } elseif ($orderData->order_status == 6) {
                        $order_status = 'Out for Delivery';
                    } else {
                        $order_status = 'Cancelled';
                    }
                    
                    if ($orderData->payment_type == 1) {
                        $payment_type = 'Online Payment';
                    } elseif ($orderData->payment_type == 3) {
                        $payment_type = 'Cash On Delivery';
                    } else {
                        $payment_type = 'Online Payment';
                    }
                    
                    // Conditionally set the payment status
                    if ($orderData->payment_type == 3) {
                        // Cash On Delivery
                        if ($orderData->order_status == 3) {
                            $payment_status = 'Success';
                        } else {
                            if ($orderData->payment_status == 1) {
                                $payment_status = 'Pending';
                            } elseif ($orderData->payment_status == 2) {
                                $payment_status = 'Success';
                            } else {
                                $payment_status = 'Failed';
                            }
                        }
                    } else {
                        // Other payment types
                        if ($orderData->payment_status == 1) {
                            $payment_status = 'Success';
                        } else {
                            $payment_status = 'Failed';
                        }
                    }
                    
                    // if ($orderData->payment_status == 1) {
                    //     $payment_status = 'Success';
                    // } else {
                    //     $payment_status = 'Failed';
                    // }
                    // if ($orderData->order_from == 1) {
                    //     $order_from = 'Web';
                    // } else if ($orderData->order_from == 2) {
                    //     $order_from = 'Android';
                    // } else {
                    //     $order_from = 'Ios';
                    // }
                    if ($orderData->order_from == 1) {
                        $order_from = 'Web';
                    } else {
                        $order_from = 'Application';
                    }
                    
                    if ($order_type == 1) {
                        $order_type = 'Online';
                    } else {
                        $order_type = 'Pickup Order';
                    }
                    
                    ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <td width="30%">
                                    <h4>
                                        <p><b>Customer Details</b></p>
                                    </h4>
                                </td>
                                <td colspan="4">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Name</b></p>
                                </td>
                                <td colspan="4">
                                    <p>{{ ucfirst(@$orderData->customer_name) }}</p>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Email</b> </p>
                                </td>
                                <td colspan="4">
                                    <p>{{ @$orderData->customer_email }}</p>
                                </td>
                            <tr>
                                <td>
                                    <p><b>Phone Number</b></p>
                                </td>
                                <td colspan="4">
                                    <p>+{{ @$orderData->phone_code }} {{ @$orderData->customer_mobile }}</p>
                                </td>
                            </tr>
                            @if ($orderData->order_type == 1)
                                {{-- <tr>    
                            <td>
                                <p><b>Billing Address</b></p>
                            </td>
                            <td colspan="4">
                                <p>{{ @$orderData->billing_address }}</p>
                            </td>
                        </tr> --}}
                                <tr>
                                    <td>
                                        <p><b>Delivery Address</b></p>
                                    </td>
                                    <td colspan="4">
                                        {{-- <p>
                                    @php
                                      echo  $delivery_address = str_replace(',|',',', $orderData->delivery_address);
                                        
                                    @endphp
                                </p> --}}
                                        <p>
                                            <?php
                                            if (strpos($orderData->delivery_address, ',|') !== false) {
                                                $delivery_address = explode(',| ', $orderData->delivery_address);
                                                //removing first element from array, it means name.
                                                $name = array_shift($delivery_address);
                                            }
                                            ?>
                                            {{ @$name }}</p>
                                        <p>
                                            {{ $delivery_address ? implode(', ', $delivery_address) : '' }}
                                        </p>
                                    </td>
                                </tr>
                            @endif
                            @if ($orderData->order_type != 2)
                                <tr>
                                    <td>
                                        <p><b>Country</b></p>
                                    </td>
                                    <td colspan="4">
                                        <p>{{ @$orderData->country_name ?: '-' }}</p>
                                    </td>
                                </tr>
                            @endif
                            @if ($orderData->order_type == 2)
                                <tr>
                                    <td>
                                        <h4>
                                            <p><b>Store Details</b></p>
                                        </h4>
                                    </td>
                                    <td colspan="4">
                                        <p><b></b></p>
                                    </td>
                                </tr>
                                <td>
                                    <p><b>Store Address</b></p>
                                </td>
                                <td colspan="4">
                                    <p>
                                        <?php
                                        if (strpos(@$orderData->store_pickup_address, ',|') !== false) {
                                            $order_pickup_address = explode(',| ', @$orderData->store_pickup_address);
                                            //removing first element from array, it means name.
                                            $name = array_shift($order_pickup_address);
                                        }
                                        ?>
                                        {{ @$name }}</p>
                                    <p>
                                        {{ @$order_pickup_address ? implode('', @$order_pickup_address) : '' }}
                                    </p>
                                </td>
                                </tr>
                            @endif

                            <tr>
                                <td>
                                    <h4>
                                        <p><b>Order Details</b></p>
                                    </h4>
                                </td>
                                <td colspan="4">
                                    <p><b></b></p>
                                </td>
                            </tr>
                            <td>
                                <p><b>Order ID</b></p>
                            </td>
                            <td colspan="4">
                                <p>{{ @$orderData->order_id }}</p>
                            </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Order from</b></p>
                                </td>
                                <td colspan="4">
                                    <p>{{ @$order_from }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Order Type</b></p>
                                </td>
                                <td colspan="4">
                                    <p>{{ @$order_type }}</p>
                                </td>
                            </tr>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Delivery Status</b></p>
                                </td>
                                <td colspan="4">
                                    <p>{{ @$order_status }}</p>
                                </td>
                            </tr>

                            @if (@$orderData->order_type != 2)
                                @if ($orderData->trans_no != '')
                                    <tr>
                                        <td>
                                            <p><b>Transaction ID</b></p>
                                        </td>
                                        <td colspan="4">
                                            <p>{{ @$orderData->trans_no }}</p>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                            <tr>
                                <td>
                                    <p><b>Payment Method</b></p>
                                </td>
                                <td colspan="4">
                                    <p>{{ @$payment_type }}</p>
                                </td>
                            </tr>
                            {{-- @if (@$orderData->order_type != 2) --}}
                                <tr>
                                    <td>
                                        <p><b>Payment status</b></p>
                                    </td>
                                    <td colspan="4">
                                        <p>{{ @$payment_status }}</p>
                                    </td>
                                </tr>
                            {{-- @endif --}}
                            <tr>
                                <td>
                                    <p><b>Order Placed</b></p>
                                </td>
                                <td colspan="4">
                                    <p>{{ \Helper::converttimeTozone($orderData->created_at) }}

                                    </p>
                                </td>
                            </tr>
                            @if ($orderData->note)
                                <tr>
                                    <td>
                                        <p><b>Note</b></p>
                                    </td>
                                    <td colspan="4">
                                        <p>{{ @$orderData->note }}</p>
                                    </td>
                                </tr>
                            @endif
                        </thead>
                        <thead>
                            <tr>
                                <td colspan="1">
                                    <h4>
                                        <p><b>Product Details</b></p>
                                    </h4>
                                </td>
                                <td colspan="4">
                                    <p><b></b></p>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <p>Product Name</p>
                                </th>
                                <th>
                                    <p>Product Size</p>
                                </th>
                                <th>
                                    <p>Qty</p>
                                </th>
                                <th>
                                    <p>Amount</p>
                                </th>
                                <th>
                                    <p>Total</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderDetails as $data)
                                <?php if ($data->variant_unit == 1) {
                                    $variant_unit = 'ML';
                                } else {
                                    $variant_unit = 'L';
                                } ?>
                                <tr>
                                    <td>
                                        <p>{{ @$data->product_name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ @$data->variant_size }} {{ $variant_unit }}</p>
                                    </td>
                                    <td>
                                        <p>{{ @$data->quantity }}</p>
                                    </td>
                                    {{-- @if (@$orderData->order_type != 2) --}}
                                    <?php
                                    // echo $data->product_original_amount;
                                    //  dd(@$data->product_original_amount);
                                    // echo $data->product_total_amount;
                                    if ($data->product_original_amount != 0 && $data->product_original_amount != '') {
                                        $product_amount = @Helper::numberFormat($data->quantity * $data->product_original_amount);
                                    } else {
                                        $product_amount = @Helper::numberFormat($data->quantity * $data->product_original_amount);
                                    }
                                    ?>
                                    <td>
                                        <p> {{ @Helper::numberFormat(@$data->product_original_amount) }}
                                            {{ @$settings->currency_symbol }} </p>
                                        {{-- <p>{{$data->product_total_amount}}  {{ @$product_amount}} {{ @$settings->currency_symbol }}</p> --}}
                                        <?php
                                        //  dd(@$data->product_total_amount);
                                        ?>
                                    </td>
                                    <td>
                                        <p> {{ @Helper::numberFormat(@$product_amount) }}
                                            {{ @$settings->currency_symbol }}</p>
                                    </td>
                                    {{-- @else
                            <td>
                                <p> {{ @$data->discount_price }} {{ @$settings->currency_symbol }}</p>
                            </td>
                            @endif
                        </tr> --}}
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="3" class="remove-border"> </td>
                                <td>
                                    <p>Sub Amount</p>
                                </td>
                                <td colspan="1">
                                    <p> {{ @Helper::numberFormat(@$orderData->total_amount) }}
                                        {{ @$settings->currency_symbol }}</p>
                                </td>
                            </tr>
                            @if ($orderData->discount_amount != 0)
                                <tr>
                                    <td colspan="3" class="remove-border"> </td>
                                    <td>
                                        <p>Discount Amount</p>
                                    </td>
                                    <td colspan="1">
                                        <p>- {{ @Helper::numberFormat(@$orderData->discount_amount) }}
                                            {{ @$settings->currency_symbol }} ( {{ $orderData->promocode_name }} )</p>
                                    </td>
                                </tr>
                            @endif
                            @php
                                $discountPrice = $orderData->total_amount - $orderData->payable_amount;
                            @endphp
                            @if ($orderData->tax != 0)
                                <tr>
                                    <td colspan="3" class="remove-border"> </td>
                                    <td>
                                        <p>Tax</p>
                                    </td>
                                    <td colspan="1">
                                        <p>{{ @Helper::numberFormat(@$orderData->tax) }} {{ @$settings->currency_symbol }}
                                        </p>
                                    </td>
                                </tr>
                            @endif
                            @if ($orderData->order_type == 1)
                                <tr>
                                    <td colspan="3" class="remove-border"> </td>
                                    <td>
                                        <p>Delivery Fee</p>
                                    </td>
                                    <td colspan="1">
                                        <p>{{ @Helper::numberFormat(@$orderData->delivery_fee) }}
                                            {{ @$settings->currency_symbol }}</p>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="remove-border"> </td>
                                <td>
                                    <p><b>Total Amount</b></p>
                                </td>
                                <td colspan="1">
                                    <p><b> {{ @Helper::numberFormat($orderData->payable_amount) }}
                                            {{ @$settings->currency_symbol }}</b></p>
                                </td>
                            </tr>

                        </tfoot>
                    </table>

                    <div class="form-group row">
                        <div class="col-sm-2">
                            <a href="{{ route('adminorder') }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                                <i class="material-icons">
                                    &#xe5cd;</i> Cancel
                            </a>
                        </div>
                        <div class="col-sm-10">

                        </div>
                    </div>

                    {{ Form::close() }}

            </div>
        </div>

    </div>
@endsection

@push('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>
    Dashboard / Product/ Sdfsdfdsf ( Avg. Rating - 0 )
    Back



    <script></script>
@endpush
