<thead>
    <tr>
        <th>No.</th>
        <th>Order Date</th>
        <th>Order Id</th>
        <th>Order Type</th>
        <th>Shop Name</th>
        <th>Customer Name</th>
        <th>Phone Number</th>
        <th>Payment Type</th>
        <th>Country</th>
        <th>Product Name</th>
        <th>Attribute</th>
        <th>Quantity</th>
        <th>M.R.P</th>
        <th>Gross Amount</th>
        <th>Promotion Discount</th>
        <th>Loyalty Discount</th>
        <th>Bogo Discount</th>
        <th>Cart Discount</th>
        <th>Total Discount</th>
        <th>Delivery Charge</th>
        <th>Gift Card</th>
        <th>Paid Amount</th>
        <th>Delivery Status</th>
        <th>Payment Status</th>
    </tr>
</thead>
<tbody>
@foreach($orders as $order)
    @if(isset($order['is_blank']) && $order['is_blank'])
        <tr><td colspan="22">&nbsp;</td></tr>
    @elseif(isset($order['is_summary']) && $order['is_summary'])
        <tr style="font-weight: bold;">
            <td colspan="11"><strong>TOTAL</strong></td>
            <td><strong>{{ $order['summary']['quantity'] }}</strong></td>
            <td></td>
            <td><strong>{{ number_format($order['summary']['gross'], 2) }}</strong></td>
            <td><strong>{{ number_format($order['summary']['promo'], 2) }}</strong></td>
            <td><strong>{{ number_format($order['summary']['loyalty'], 2) }}</strong></td>
            <td><strong>{{ number_format($order['summary']['bogo'], 2) }}</strong></td>
            <td><strong>{{ number_format($order['summary']['cart'], 2) }}</strong></td>
            <td><strong>{{ number_format($order['summary']['discount'], 2) }}</strong></td>
            <td><strong>{{ number_format($order['summary']['delivery'], 2) }}</strong></td>
            <td><strong>{{ number_format($order['summary']['gift'], 2) }}</strong></td>
            <td><strong>{{ number_format($order['summary']['paid'], 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
    @else
        @php $data = $order['data']; $c = $order['computed']; @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d h:i A') }}</td>
            <td>{{ $data->order_id }}</td>
            <td>{{ $data->order_type == 1 ? 'Online' : 'Pickup' }}</td>
            <td>{{ explode(',', $data->store_address)[0] ?? '-' }}</td>
            <td>{{ $data->customer_name }}</td>
            <td>{{ '+' . $data->customer_mobile }}</td>
            <td>{{ $data->payment_type == 1 ? 'Online' : 'COD' }}</td>
            <td>{{ $data->country_name }}</td>
            <td>{{ $data->product_name }}</td>
            <td>{{ $data->attribute }} {{ $data->size == 1 ? 'ML' : 'L' }}</td>
            <td>{{ $data->quantity }}</td>
            {{-- <td>{{ isset($data->effective_quantity) ? $data->effective_quantity : $data->quantity }}</td> --}}
            {{-- <td>{{ 'GH₵ ' . number_format($data->mrp, 2) }}</td> --}}
            <td>{{ 'GH₵ ' . number_format(($data->total_amount && $data->total_amount > 0) ? $data->total_amount : $data->mrp, 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['item_total'], 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['promo_discount'], 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['loyalty_discount'], 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['bogo_discount'], 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['cart_discount'], 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['total_discount'], 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['delivery_charge'], 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['gift_card'], 2) }}</td>
            <td>{{ 'GH₵ ' . number_format($c['grand_total'], 2) }}</td>
            <td>{{ $data->delivery_status }}</td>
            {{-- <td>{{ $data->payment_status == 2 ? 'Success' : ($data->payment_status == 1 ? 'Pending' : 'Failed') }}</td> --}}
            <td>
                @if ($data->payment_type == 3) {{-- COD --}}
                    @if ($data->order_status == 3)
                        Success
                    @else
                        @if ($data->payment_status == 1)
                            Pending
                        @elseif ($data->payment_status == 2)
                            Success
                        @else
                            Failed
                        @endif
                    @endif
                @else {{-- Online or other payment types --}}
                    @if ($data->payment_status == 1)
                        Success
                    @else
                        Failed
                    @endif
                @endif
            </td>

        </tr>
    @endif
@endforeach
</tbody>
