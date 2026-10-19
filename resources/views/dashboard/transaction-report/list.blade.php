@extends('dashboard.layouts.master')
@push('after-styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <style>
        #transaction td:not(.first-td) {
            padding: 16px !important;
        }
    </style>
@endpush
@section('title', 'Transaction Report')
@section('content')
    <div class="padding website-label">

        <div class="box">

            <div class="box-header dker">
                <h3>Transaction Report</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>Transaction Report</span>
                </small>
            </div>
            <div class="card-body" style="margin-left: 10px;">
                <div class="form-group">
                    <div class="col-md-3" style="margin-top: 22px;">
                        <label>Start Date</label>
                        <input type="text" class="form-control" name="startdate" id="startdate" placeholder="MM/DD/YYYY" readonly>
                    </div>
                    <div class="col-md-3" style="margin-top: 22px;">
                        <label>End Date</label>
                        <input type="text" class="form-control" name="enddate" id="enddate" placeholder="MM/DD/YYYY" readonly>
                    </div>

                    <div class="col-md-2 clear_button" style="margin-top: 22px;">
                        <label>&nbsp;</label>
                        <a onclick="location.reload();">
                            <button type="button" class="btn btn-danger mr-2 mb-5" style="margin-top: 30px;">Clear</button>
                        </a>

                    </div>

                </div>

            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        {{ Form::open(['route' => 'export_transactionreport', 'method' => 'post', 'style="background :none !important;"', 'id' => 'export']) }}

                        <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : '' }}"
                            id="export_start_date">
                        <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : '' }}"
                            id="export_end_date">
                        <a class="btn btn-fw primary export-form" href="javascript:void(0)">
                            Export In Excel
                        </a>
                        {{ Form::close() }}
                    </li>

                    <li class="nav-item inline">
                        {{ Form::open(['route' => 'export_transactionpdf', 'method' => 'post', 'style="background :none !important;"', 'id' => 'exportpdf']) }}

                        <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : '' }}"
                            id="export_pdf_start_date">
                        <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : '' }}"
                            id="export_pdf_end_date">
                        <a class="btn btn-fw primary export-pdfform" href="javascript:void(0)">
                            Export In PDF
                        </a>
                        {{ Form::close() }}
                    </li>

                </ul>
            </div>
            <div class="box-body">
                <form>

                    <div class="table-responsive">
                        <table class="table table-bordered m-a-0" id="transaction">
                            <thead class="dker">
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Transaction Date/Time</th>
                                    <!-- <th>Booking Title</th> -->
                                    <th>Customer Name</th>
                                    <th>Service Provider Name</th>
                                    <th>Total Amount</th>
                                    <th>{{ __('backend.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
@push('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript">
        var path = "{{ route('booking.autocomplete') }}";

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            load_data();

            function load_data(startdate, enddate, user_name, area_val) {
                var action_url = "{!! route('transactionreport.anyData') !!} ";

                var dataTable = $('#transaction').DataTable({
                    @if (!in_array(Helper::GeneralSiteSettings('per_page_limit'), [10, 25, 50, 100]))
                        lengthMenu: [
                            [10, 25, 50, 100, "{{ Helper::GeneralSiteSettings('per_page_limit') }}"]
                            .sort(function(a, b) {
                                return a - b
                            }),
                            [10, 25, 50, 100, "{{ Helper::GeneralSiteSettings('per_page_limit') }}"]
                            .sort(function(a, b) {
                                return a - b
                            }),
                        ],
                    @endif
                    pageLength: "{{ Helper::GeneralSiteSettings('per_page_limit') }}",
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    columnDefs: [{
                        'bSortable': false,
                        'aTargets': [5]
                    }],
                    ajax: {
                        url: action_url,
                        type: 'POST',
                        data: function(d) {
                            return $.extend({}, d, {
                                "startdate": $("#startdate").val().toLowerCase(),
                                "enddate": $("#enddate").val().toLowerCase(),
                                "user_name": $('#user_name').val(),
                                "area_val": $('#search').val(),
                            });
                        }
                    },
                    columns: [{
                            data: 'trans_no',
                            name: 'trans_no',

                        },
                        {
                            data: 'trans_date',
                            name: 'trans_date',

                        },
                        {
                            data: 'user_name',
                            name: 'user_name',

                        },
                        {
                            data: 'provider_name',
                            name: 'provider_name',

                        },
                        {
                            data: 'amount',
                            name: 'amount',

                        },
                        {
                            data: 'status',
                            name: 'status',

                        }
                    ],
                    order: [[1, 'desc']]
                });

                $('#startdate, #enddate').change(function() {
                    {{-- let start_date = $('#startdate').val();
                    let end_date = $('#enddate').val();
                    load_data(start_date, end_date); --}}

                    $("#export_start_date").val($("#startdate").val().toLowerCase());
                    $("#export_pdf_start_date").val($("#startdate").val().toLowerCase());
                    $("#export_pdf_end_date").val($("#enddate").val().toLowerCase());
                    $("#export_end_date").val($("#enddate").val().toLowerCase());

                    dataTable.draw();
                });

                $('#user_name').change(function() {
                    $("#transaction").dataTable().fnDestroy();
                    var value = $(this).val();
                    let start_date = $('#startdate').val();
                    let end_date = $('#enddate').val();
                    load_data(start_date, end_date, value);

                    dataTable.draw();
                });

                $("#search").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: path,
                            type: 'POST',
                            dataType: "json",
                            data: {
                                search: request.term
                            },
                            success: function(data) {
                                response(data);
                            }
                        });
                    },
                    select: function(event, ui) {
                        $('#search').val(ui.item.label);
                        $("#transaction").dataTable().fnDestroy();
                        let start_date = $('#startdate').val();
                        let end_date = $('#enddate').val();
                        let user_name = $('#user_name').val();
                        let area_val = ui.item.value;
                        console.log(ui.item.value);
                        load_data(start_date, end_date, user_name, area_val);
                        dataTable.draw();
                        return false;
                    }
                });
            }

        });
        $("#checkAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $(".export-form").click(function() {
            $('#export').submit();
        });

        $(".export-pdfform").click(function() {
            $('#exportpdf').submit();
        });

        $(document).ready(function() {
            $("#startdate").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                orientation: "bottom",
                autoclose: true
            });

            var start_date = $('#startdate');
            var end_date = $('#enddate');

            start_date.on('change', function() {
                var minDate = new Date(start_date.val());
                end_date.datepicker('option', 'minDate', minDate);
            });

            // Disable the end_date input field
            end_date.prop('disabled', true);

            // Enable the end_date input field when the start_date input field is changed
            start_date.on('change', function() {
                end_date.prop('disabled', false);
            });

            $("#enddate").datepicker({
                changeMonth: true,
                endDate: '+0d',
                changeYear: true,
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                orientation: "bottom",
                autoclose: true
            });


        });
    </script>
@endpush
