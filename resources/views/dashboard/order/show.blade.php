@extends('dashboard.layouts.master')
@section('title', 'Order Details | Admin Panel')
@section('content')
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />

    <style>
        .remove-border { border-top: none !important; }
        .invoice-paper-inside {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            overflow: hidden;
            font-size: 11.5px;
            color: #242424;
        }

        .invoice-section-title {
            background-color: #212529;
            color: #ffffff;
            padding: 4px 8px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .invoice-slip-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-top: 8px;
        }
        .invoice-slip-table th {
            background-color: #212529;
            color: #ffffff;
            font-weight: 700;
            padding: 6px 8px;
            border: 1px solid #374151;
            font-size: 11px;
        }
        .invoice-slip-table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .invoice-slip-table tbody tr:hover { background-color: #fafafa; }
        .badge {
            font-size: 11px;
            padding: 3px 8px;
            font-weight: 600;
            border-radius: 4px;
        }
        .badge-warning { background-color: #f59e0b; color: #fff; }
        .badge-info { background-color: #0284c7; color: #fff; }
        .badge-success { background-color: #10b981; color: #fff; }
        .badge-danger { background-color: #ef4444; color: #fff; }
        .badge-primary { background-color: #6366f1; color: #fff; }
    </style>

    <div class="padding edit-package website-label-show">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe02e;</i> {{ __('backend.view') }} Order Details</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <a href="{{ route('adminorder') }}">Order Management</a> /
                    <span>{{ __('backend.view') }} Order Details</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('adminorder') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                <?php
                $method = DB::table('transactions')->where('order_id', $orderData->id)->first();
                $order_type = @$orderData->order_type;
                $order_status = @$orderData->order_status;
                
                if ($orderData->payment_type == 1) {
                    $payment_type = 'Online Payment';
                } elseif ($orderData->payment_type == 3) {
                    $payment_type = 'Cash On Delivery';
                } else {
                    $payment_type = 'Online Payment';
                }
                
                if ($orderData->order_status == 1) {
                    $order_status_label = 'Pending';
                    $order_status_badge = 'badge-warning';
                } elseif ($orderData->order_status == 2) {
                    $order_status_label = 'Accepted';
                    $order_status_badge = 'badge-info';
                } elseif ($orderData->order_status == 3) {
                    $order_status_label = 'Delivered';
                    $order_status_badge = 'badge-success';
                } elseif ($orderData->order_status == 5) {
                    $order_status_label = 'Ready to Pick Up';
                    $order_status_badge = 'badge-primary';
                } elseif ($orderData->order_status == 6) {
                    $order_status_label = 'Out for Delivery';
                    $order_status_badge = 'badge-info';
                } else {
                    $order_status_label = 'Cancelled';
                    $order_status_badge = 'badge-danger';
                }

                if ($orderData->payment_type == 3) {
                    if ($orderData->order_status == 3) {
                        $payment_status_label = 'Success';
                        $payment_status_badge = 'badge-success';
                    } else {
                        if ($orderData->payment_status == 1) {
                            $payment_status_label = 'Pending';
                            $payment_status_badge = 'badge-warning';
                        } elseif ($orderData->payment_status == 2) {
                            $payment_status_label = 'Success';
                            $payment_status_badge = 'badge-success';
                        } else {
                            $payment_status_label = 'Failed';
                            $payment_status_badge = 'badge-danger';
                        }
                    }
                } else {
                    if ($orderData->payment_status == 1) {
                        $payment_status_label = 'Success';
                        $payment_status_badge = 'badge-success';
                    } elseif ($orderData->payment_status == 2) {
                        $payment_status_label = 'Failed';
                        $payment_status_badge = 'badge-danger';
                    } else {
                        $payment_status_label = 'Pending';
                        $payment_status_badge = 'badge-warning';
                    }
                }

                if ($orderData->order_from == 1) {
                    $order_from = 'Web';
                } else {
                    $order_from = 'Application';
                }

                if ($order_type == 1) {
                    $order_type_str = 'Online';
                } else {
                    $order_type_str = 'Pickup Order';
                }

                $delInfo = Helper::formatOrderAddress($orderData->delivery_address, $orderData->customer_mobile ?? null);
                $orderLat = $delInfo['lat'] ?? null;
                $orderLng = $delInfo['lng'] ?? null;

                $billInfo = !empty($orderData->billing_address) ? Helper::formatOrderAddress($orderData->billing_address, $orderData->customer_mobile ?? null) : null;
                $billLat = $billInfo['lat'] ?? null;
                $billLng = $billInfo['lng'] ?? null;

                $pickupName = '';
                $pickupLines = [];
                if (strpos(@$orderData->store_pickup_address, ',|') !== false) {
                    $order_pickup_address = explode(',| ', @$orderData->store_pickup_address);
                    $pickupName = array_shift($order_pickup_address);
                    $pickupLines = $order_pickup_address;
                }

                // Clean and resolve Customer Phone Number (handling guest orders with phone_code = 0)
                $rawMobile = trim((string)(@$orderData->customer_mobile ?: @$orderData->phone ?: ''));
                $rawMobile = preg_replace('/^0\s*/', '', $rawMobile);
                $rawMobile = trim($rawMobile);

                $phoneCode = (!empty($orderData->phone_code) && $orderData->phone_code != '0') ? '+' . ltrim($orderData->phone_code, '+') : '';

                if (!empty($rawMobile) && $rawMobile !== '0') {
                    $customerPhone = str_starts_with($rawMobile, '+') ? $rawMobile : (($phoneCode ? $phoneCode . ' ' : '+') . $rawMobile);
                } elseif (!empty($delInfo['phone'])) {
                    $customerPhone = $delInfo['phone'];
                } else {
                    $customerPhone = '-';
                }

                // Clean and resolve Customer Name (replace dummy placeholder for guest users)
                $isDummyName = empty($orderData->customer_name) || strtolower(trim($orderData->customer_name)) === 'dummy dummy';
                $customerDisplayName = !$isDummyName 
                    ? ucfirst($orderData->customer_name) 
                    : (!empty($delInfo['name']) ? $delInfo['name'] . ' (Guest)' : 'Guest Customer');
                ?>

                {{-- Direct Print Invoice Layout rendered inside Box --}}
                <div class="invoice-paper-inside">
                    <div class="p-a" style="padding: 14px;">
                        {{-- 2 Columns: Purchase Invoice vs Delivery/Store Address --}}
                        <div class="row m-b-sm">
                            <div class="col-sm-6">
                                <div class="invoice-section-title">PURCHASE INVOICE</div>
                                <p style="margin: 0; line-height: 1.6;">
                                    <strong>Order No.</strong> - #{{ $orderData->order_id }}<br>
                                    <strong>Order Date</strong> - {{ \Helper::converttimeTozone($orderData->created_at) }}<br>
                                    <strong>Order Type</strong> - {{ $order_type_str }} (Via {{ $order_from }})<br>
                                    <strong>Delivery Status</strong> - <span class="badge {{ $order_status_badge }}">{{ $order_status_label }}</span><br>
                                    <strong>Payment Method</strong> - {{ $payment_type }} <span class="badge {{ $payment_status_badge }}">{{ $payment_status_label }}</span>
                                    @if(!empty($orderData->trans_no))
                                        <br><strong>Transaction ID</strong> - <code>{{ $orderData->trans_no }}</code>
                                    @endif
                                    @if ($orderData->order_status == 4 && !empty($userName))
                                        <br><span class="text-danger"><strong>Cancelled By:</strong> {{ $userName }}</span>
                                    @endif
                                    @if(!empty($orderData->note))
                                        <br><strong>Note:</strong> "{{ $orderData->note }}"
                                    @endif
                                    @if(!empty($orderData->recipientName) || !empty($orderData->giftMessage))
                                        <br><strong>Gift For:</strong> {{ @$orderData->recipientName }} | <em>"{{ @$orderData->giftMessage }}"</em>
                                    @endif
                                </p>
                            </div>
                            <div class="col-sm-6">
                                @if ($orderData->order_type == 1)
                                    <div class="invoice-section-title">DELIVERY & CUSTOMER DETAILS</div>
                                    <div  style="margin: 0; line-height: 1.6;">
                                        <strong>Customer</strong> - {{ $customerDisplayName }}
                                        @if(!empty($orderData->customer_email)) | <strong>Email</strong> - {{ $orderData->customer_email }} @endif<br>
                                        <strong>Phone</strong> - {{ $customerPhone }}<br>
                                        <div class="m-t-xs pt-xs" style="border-top: 2px dashed #e2e8f0;"></div>
                                        <strong>Recipient</strong> - {{ @$delInfo['name'] }} @if(!empty($delInfo['phone'])) ({{ $delInfo['phone'] }}) @endif<br>
                                        <strong>Shiping Address</strong> - {{ @$delInfo['address'] }}
                                        @if(!empty($orderLat) && !empty($orderLng))
                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#orderLocationMapModal" class="btn btn-xs btn-primary" style="padding: 1px 7px; margin-left: 6px; vertical-align: middle; border-radius: 3px; font-size: 11px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" title="View on Google Maps ({{ $orderLat }}, {{ $orderLng }})">
                                                <i class="fa fa-map-marker-alt" style="color: #ffeb3b;"></i> <span>Map</span>
                                            </a>
                                        @endif
                                       
                                    </div >
                                    @if ($orderData->delivery_options || !empty($orderData->delivery_instructions))
                                        <p style="margin: 4px 0 0 0; line-height: 1.4; color: #475569;">
                                            <strong>Drop-off:</strong> {{ $orderData->delivery_options == 1 ? 'Hand over to Me' : 'Leave at Door' }}
                                            @if(!empty($orderData->delivery_instructions)) - {{ $orderData->delivery_instructions }} @endif
                                        </p>
                                    @endif
                                    @if ($orderData->billing_address && !empty($orderData->billing_address))
                                        <div class="m-t-xs pt-xs" style="border-top: 2px dashed #e2e8f0;">
                                            <strong>Billing Address:</strong> {{ @$billInfo['name'] }} - {{ @$billInfo['address'] }}
                                        </div>
                                    @endif
                                @else
                                    <div class="invoice-section-title">STORE & CUSTOMER DETAILS</div>
                                    <p style="margin: 0; line-height: 1.6;">
                                        <strong>Store Name</strong> - {{ $pickupName }}<br>
                                        <strong>Address</strong> - {{ @$order_pickup_address ? implode(', ', @$order_pickup_address) : '-' }}<br>
                                        <strong>Customer</strong> - {{ $customerDisplayName }}<br>
                                        <strong>Phone</strong> - {{ $customerPhone }}
                                        @if(!empty($orderData->customer_email))
                                            <br><strong>Email</strong> - {{ $orderData->customer_email }}
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Items Table --}}
                        <table class="invoice-slip-table">
                            <thead>
                                <tr>
                                    <th style="width: 48%;">ITEM</th>
                                    <th style="width: 14%; text-align: center;">ITEM SIZE</th>
                                    <th style="width: 10%; text-align: center;">QTY</th>
                                    <th style="width: 14%; text-align: right;">UNIT PRICE</th>
                                    <th style="width: 14%; text-align: right;">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderDetails as $data)
                                <?php 
                                if ($data->variant_unit == 1) {
                                    $variant_unit = 'ML';
                                } else {
                                    $variant_unit = 'L';
                                } 
                                $display_qty = $data->quantity;
                                $unit_price = ($data->product_total_amount && $data->product_total_amount > 0) ? $data->product_total_amount : $data->product_original_amount;
                                $product_amount = Helper::numberFormat($display_qty * $unit_price);
                                ?>
                                <tr>
                                    <td>
                                        <strong>{{ @$data->product_name }}</strong>
                                        @if($data->is_bogo)
                                            <span class="badge badge-warning" style="font-size: 9px;"><i class="fa fa-tag"></i> BOGO</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">{{ @$data->variant_size }} {{ $variant_unit }}</td>
                                    <td style="text-align: center; font-weight: 700;">{{ @$data->quantity }}</td>
                                    <td style="text-align: right;">
                                        @if ($data->product_total_amount && $data->product_total_amount < $data->product_original_amount)
                                            <span style="text-decoration: line-through; color: #94a3b8; font-size: 11px;">
                                                {{ Helper::numberFormat($data->product_original_amount) }}
                                            </span>&nbsp;
                                            <span class="text-success font-weight-bold">
                                                {{ Helper::numberFormat($data->product_total_amount) }} {{ @$settings->currency_symbol }}
                                            </span>
                                        @else
                                            {{ Helper::numberFormat($unit_price) }} {{ @$settings->currency_symbol }}
                                        @endif
                                    </td>
                                    <td style="text-align: right; font-weight: 700;">
                                        {{ $product_amount }} {{ @$settings->currency_symbol }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- 2-Column Summary: Billing Summary vs Tax Summary --}}
                        <div class="row m-t-sm" style="border: 1px solid #e5e7eb; margin-left: 0; margin-right: 0; background: #fafafa; border-radius: 4px;">
                            {{-- Billing Summary --}}
                            <div class="col-sm-6 p-a-xs" style="border-right: 1px solid #e5e7eb;">
                                <div style="font-weight: 700; color: #111; font-size: 11.5px; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px;">
                                    Billing Summary
                                </div>
                                <table style="width: 100%; font-size: 11.5px; line-height: 1.8;">
                                    <tr>
                                        <td>Total Sale</td>
                                        <td align="right">{{ @Helper::numberFormat(@$totalAmount) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    @if (!empty($orderData->discount_amount) && $orderData->discount_amount > 0)
                                    <tr>
                                        <td>Coupon Discount ({{ $orderData->promocode_name }})</td>
                                        <td align="right">(-) {{ @Helper::numberFormat($orderData->discount_amount) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    @endif
                                    @if ($bogoDiscount > 0)
                                    <tr>
                                        <td>Bogo Discount</td>
                                        <td align="right">(-) {{ @Helper::numberFormat($bogoDiscount) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    @endif
                                    @if (!empty($orderData->cart_discount) && $orderData->cart_discount > 0)
                                    <tr>
                                        <td>Cart Discount</td>
                                        <td align="right">(-) {{ @Helper::numberFormat($orderData->cart_discount) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    @endif
                                    @if (!empty($orderData->reward_amount) && $orderData->reward_amount > 0)
                                    <tr>
                                        <td>Reward Discount</td>
                                        <td align="right">(-) {{ @Helper::numberFormat($orderData->reward_amount) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    @endif
                                    <tr style="border-top: 1px solid #cbd5e1; font-weight: 700;">
                                        <td>Total (After Discount)</td>
                                        <td align="right">
                                            {{ @Helper::numberFormat(@$totalAmount - @$totalDiscount) }} {{ @$settings->currency_symbol }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Tax Summary --}}
                            <div class="col-sm-6 p-a-xs">
                                <div style="font-weight: 700; color: #111; font-size: 11.5px; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px;">
                                    Tax Summary
                                </div>
                                <table style="width: 100%; font-size: 11.5px; line-height: 1.8;">
                                    <tr>
                                        <td>(i) Tax Exclusive Value</td>
                                        <td align="right">{{ @Helper::numberFormat(@$subTotal) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    <tr>
                                        <td>(ii) NHIL (2.5%)</td>
                                        <td align="right">{{ @Helper::numberFormat(@$nhil) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    <tr>
                                        <td>(iii) Get Funds Levy (2.5%)</td>
                                        <td align="right">{{ @Helper::numberFormat(@$getFund) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    <tr>
                                        <td>(iv) VAT 15%</td>
                                        <td align="right">{{ @Helper::numberFormat(@$vat) }} {{ @$settings->currency_symbol }}</td>
                                    </tr>
                                    <tr style="border-top: 1px solid #cbd5e1; font-weight: 700;">
                                        <td>Total Tax Inclusive Value</td>
                                        <td align="right">
                                            {{ round(@Helper::numberFormat(@$subTotal + @$nhil + @$getFund + @$vat)) }} {{ @$settings->currency_symbol }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Total Bar --}}
                        <div class="m-t-xs p-a-xs" style="background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 4px;">
                            <table style="width: 100%; font-size: 12px;">
                                @if (!empty($orderData->gift_card) && $orderData->gift_card > 0)
                                <tr>
                                    <td>Gift Card:</td>
                                    <td align="right">(-) {{ @Helper::numberFormat(@$orderData->gift_card) }} {{ @$settings->currency_symbol }}</td>
                                </tr>
                                @endif
                                @if ($orderData->order_type == 1)
                                <tr>
                                    <td>Delivery Fee:</td>
                                    <td align="right">{{ @Helper::numberFormat(@$orderData->delivery_fee) }} {{ @$settings->currency_symbol }}</td>
                                </tr>
                                @endif
                                <tr style="border-top: 2px solid #212529; font-size: 13.5px;">
                                    <td style="padding-top: 6px;">
                                        <strong>Total (paid by client taxes inclusive)</strong>
                                    </td>
                                    <td align="right" style="padding-top: 6px; font-weight: 800; font-size: 16px; color: #111;">
                                        {{ @Helper::numberFormat($orderData->payable_amount) }} {{ @$settings->currency_symbol }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="form-group row m-t-1 m-b-0">
                    <div class="col-sm-6">
                        <a href="{{ route('adminorder') }}" class="btn btn-default">
                            <i class="material-icons">&#xe5cd;</i> Cancel
                        </a>
                        <a href="{{ route('adminorder.print', ['id' => $orderData->id]) }}" class="btn btn-default" target="_blank">
                            <i class="fas fa-print"></i>&nbsp; Print Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($orderLat) && !empty($orderLng))
    <div class="modal fade" id="orderLocationMapModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-map-marker-alt text-danger"></i> Delivery Location Map</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row m-b-0">
                        <div class="col-sm-12">
                            <iframe 
                                id="deliveryMapIframe"
                                data-original-src="https://maps.google.com/maps?q={{ $orderLat }},{{ $orderLng }}&hl=en&z=16&output=embed"
                                src="https://maps.google.com/maps?q={{ $orderLat }},{{ $orderLng }}&hl=en&z=16&output=embed"
                                width="100%" 
                                height="400" 
                                frameborder="0" 
                                style="border: 1px solid #ddd; border-radius: 4px; display: block;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-reset-map" title="Reset map to selected location">
                        <i class="fa fa-crosshairs"></i> Reset Location
                    </button>
                    <a href="https://www.google.com/maps?q={{ $orderLat }},{{ $orderLng }}" target="_blank" class="btn btn-primary">
                        <i class="fa fa-map-marker-alt"></i> Open in Google Maps
                    </a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('after-scripts')
<script>
    $(document).ready(function() {
        $('#orderLocationMapModal').on('show.bs.modal', function () {
            var $iframe = $(this).find('iframe');
            var originalSrc = $iframe.attr('data-original-src') || $iframe.attr('src');
            if (originalSrc) {
                $iframe.attr('src', '');
                setTimeout(function() {
                    $iframe.attr('src', originalSrc);
                }, 50);
            }
        });

        $(document).on('click', '.btn-reset-map', function() {
            var $modal = $(this).closest('.modal');
            var $iframe = $modal.find('iframe');
            var originalSrc = $iframe.attr('data-original-src') || $iframe.attr('src');
            if (originalSrc) {
                $iframe.attr('src', '');
                setTimeout(function() {
                    $iframe.attr('src', originalSrc);
                }, 50);
            }
        });
    });
</script>
@endpush
