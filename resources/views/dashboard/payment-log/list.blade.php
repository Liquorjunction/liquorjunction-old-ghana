@extends('dashboard.layouts.master')
@section('title', 'Payment logs | Admin Panel')
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <div class="padding website-label">
        <div class="box">
            <div class="box-header dker">
                <h3>Payment logs</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>Payment logs</span>
                </small>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-2" style="margin-bottom: 15px;">
                        <label>From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-control">
                    </div>
                    <div class="col-md-2" style="margin-bottom: 15px;">
                        <label>To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control">
                    </div>
                    <div class="col-md-2" style="margin-bottom: 15px;">
                        <label>Order id</label>
                        <input type="text" name="order_id_filter" id="order_id_filter" class="form-control" placeholder="Order id">
                    </div>
                    <div class="col-md-2" style="margin-bottom: 15px;">
                        <label>Result</label>
                        <select name="result_filter" id="result_filter" class="form-control">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="success">Success</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2" style="margin-bottom: 15px; margin-top: 25px;">
                        <button type="button" id="clear_logs" class="btn btn-danger">Clear</button>
                    </div>
                </div>
                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-bordered m-a-0" id="payment_logs">
                        <thead class="dker">
                            <tr>
                                <th>Time</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Event</th>
                                <th>Result</th>
                                <th>Detail</th>
                                <th>Source</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var dataTable = $('#payment_logs').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                order: [[0, 'desc']],
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                ajax: {
                    url: "{!! route('paymentlog.anyData') !!}",
                    type: 'POST',
                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.order_id_filter = $('#order_id_filter').val();
                        d.result_filter = $('#result_filter').val();
                    }
                },
                columns: [
                    { data: 'created_at', name: 'created_at' },
                    { data: 'order_id', name: 'order_id' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'event', name: 'event' },
                    { data: 'result', name: 'result' },
                    { data: 'detail', name: 'detail' },
                    { data: 'source', name: 'source' }
                ]
            });

            $('#from_date, #to_date, #result_filter').change(function() {
                dataTable.draw();
            });

            $('#order_id_filter').on('keyup change', function() {
                dataTable.draw();
            });

            $('#clear_logs').click(function() {
                $('#from_date, #to_date, #order_id_filter').val('');
                $('#result_filter').val('');
                dataTable.search('').draw();
            });
        });
    </script>
@endpush
