@extends('dashboard.layouts.master')
@section('title', 'Payment logs | Admin Panel')
@section('content')
<style type="text/css">
    .clear_button{
        margin-top: 30px;
    }
    .dker {
        padding-top: 0px !important;
        padding-bottom: 0px !important;
    }
    .manage-space {
        white-space: nowrap;
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" /> 
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<div class="loader" id="loader"></div> 
<div class="padding website-label">
    <div class="success_message" style="margin-bottom: 10px;"></div>
    <div id="success_file_popup" style="margin-bottom: 10px;"></div>
    <div class="box">           
        <div class="box-header dker">
            <h3>Payment logs</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / Payment logs
            </small>
        </div>
        <div class="padding general-setting">
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="col-md-2">
                    <label>From Date</label>
                    <input type="text" class="form-control" name="from_date" id="from_date" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label>To Date</label>
                    <input type="text" class="form-control" name="to_date" id="to_date" placeholder="DD-MM-YYYY">
                </div>                    
                <div class="col-md-3">
                    <label>Order id</label>
                    <input type="text" name="order_id_filter" id="order_id_filter" class="form-control" placeholder="Order id">
                </div>
                <div class="col-md-3">
                    <label>Result</label>
                    <select name="result_filter" id="result_filter" class="form-control">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-2 clear_button">
                    <label>&nbsp;</label>
                    <a onclick="location.reload();">
                        <button type="button" class="btn btn-danger mr-2 mb-5">Clear</button>
                    </a>
                </div>
            </div>
              
            <form>
                <div class="table-responsive" style="margin-top: 85px;">
                    <table class="table table-bordered m-a-0 manage-space" id="payment_logs">
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
                <footer class="dker p-a">
                    <div class="row">
                        <div class="col-sm-6 text-right text-center-xs"></div>
                    </div>
                </footer>
            </form>
        </div>
    </div>
</div>
@endsection
@push('after-scripts')
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function formatDateForBackend(dStr) {
                if (!dStr) return '';
                dStr = $.trim(dStr);
                var parts = dStr.split(/[-/]/);
                if (parts.length === 3) {
                    if (parts[0].length === 2 && parts[2].length === 4) {
                        return parts[2] + '-' + parts[1] + '-' + parts[0];
                    } else if (parts[0].length === 4) {
                        return parts[0] + '-' + parts[1] + '-' + parts[2];
                    }
                }
                return dStr;
            }

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
                        d.from_date = formatDateForBackend($('#from_date').val());
                        d.to_date = formatDateForBackend($('#to_date').val());
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

            $("#from_date").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                orientation: "bottom",
                autoclose: true
            }).on('changeDate', function(selected) {
                if (selected && selected.date) {
                    var minDate = new Date(selected.date.valueOf());
                    $('#to_date').datepicker('setStartDate', minDate);
                }
                $('#from_date').trigger('change');
            });

            $("#to_date").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                orientation: "bottom",
                autoclose: true
            }).on('changeDate', function(selected) {
                if (selected && selected.date) {
                    var minDate = new Date(selected.date.valueOf());
                    $('#from_date').datepicker('setEndDate', minDate);
                }
                $('#to_date').trigger('change');
            });

            $('#from_date, #to_date, #result_filter').on('change input', function() {
                dataTable.draw();
            });

            $(document).on('input keyup', '.dataTables_filter input', function(e) {
                dataTable.search(this.value).draw();
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
