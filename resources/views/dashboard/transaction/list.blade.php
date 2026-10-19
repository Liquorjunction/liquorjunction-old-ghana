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
@section('title', 'Transaction')
@section('content')
    <div class="padding website-label">

        <div class="box">

            <div class="box-header dker">
                <h3>Transaction</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>Transaction</span>
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
            <div class="box-body">
                <form>
                    <div class="table-responsive">
                        <table class="table table-bordered m-a-0" id="transaction">
                            <thead class="dker">
                                <tr>
                                    <th class="width20 dker">
                                        <label class="ui-check m-a-0">
                                            <input id="checkAll" type="checkbox"><i></i>
                                        </label>
                                    </th>
                                    <th>Transaction ID</th>
                                    <th>Transaction Date/Time</th>
                                    <!-- <th>Booking Title</th> -->
                                    <th>Customer Name</th>
                                    <th>Service Provider Name</th>
                                    <th>Total Amount</th>
                                    <th>{{ __('backend.status') }}</th>
                                    <th>{{ __('backend.options') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

            <!-- .modal -->
            <div id="delete_modal" class="modal fade" data-backdrop="true">
                <div class="modal-dialog" id="animate">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmation</h5>
                        </div>
                        <div class="modal-body p-lg">
                            <p>
                                {{ __('backend.confirmationDeleteMsg') }}
                                <br>
                                <strong id="show_name"> </strong>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn dark-white p-x-md"
                                data-dismiss="modal">{{ __('backend.no') }}</button>
                            <a href="javascript:void(0);"
                                class="btn danger confirmDelete p-x-md">{{ __('backend.yes') }}</a>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>
            <!-- / .modal -->

            {{-- bulk action confirmation model --}}
            <div class="modal fade" id="confirm_bulk_update" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('backend.confirmation') }}</h5>
                        </div>
                        <div class="modal-body p-lg">
                            <p class="dynamic_message">
                            </p>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn dark-white p-x-md"
                                data-dismiss="modal">{{ __('backend.no') }}</button>
                            <button type="button"
                                onclick="event.preventDefault();document.getElementById('updateAll').submit();"
                                class="btn btn-danger yes_click">{{ __('backend.yes') }}</button>
                        </div>
                    </div>
                </div>
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
        var dataTable = "";

        function confirmBulkAction(element) {
            if ($("input[name='ids[]']:checked").length == 0) {
                event.preventDefault();
                alert("Select at least one record!");
                return false;
            }
            let selected = $('#action').find(":selected").val();
            if (selected == "no") {
                event.preventDefault();
                alert("Select bulk action!");
                return false;
            }
            $("#confirm_bulk_update").modal('show');
            let message = $('#action').find(":selected").data('msg');
            $('.dynamic_message').html(message);
        }

        function deleteData(element) {
            let user_name = $(element).data('name');
            let href = $(element).data('href');

            // $('#show_name').text(user_name);
            $('.confirmDelete').attr('href', href);
            $("#delete_modal").modal('show')
        }

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            load_data();

            function load_data(startdate, enddate) {
                var action_url = "{!! route('transaction.anyData') !!} ";

                dataTable = $('#transaction').DataTable({
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
                        'aTargets': [0, 6, 7]
                    }],
                    ajax: {
                        url: action_url,
                        type: 'POST',
                        data: function(d) {
                            return $.extend({}, d, {
                                "startdate": $("#startdate").val().toLowerCase(),
                                "enddate": $("#enddate").val().toLowerCase(),
                            });
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            className: 'first-td'
                        },
                        {
                            data: 'transaction_id',
                            name: 'transaction_id',

                        },
                        {
                            data: 'created_at',
                            name: 'created_at',

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

                        },
                        {
                            data: 'options',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: ['0', 'DESC']
                });
            }

        });

        $('#startdate, #enddate').change(function() {
            dataTable.draw();
        });

        $("#checkAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        // Checkbox checked
        function checkcheckbox() {
            // Total checkboxes
            var length = $('.checkbox').length;
            // Total checked checkboxes
            var totalchecked = 0;
            $('.checkbox').each(function() {
                if ($(this).is(':checked')) {
                    totalchecked += 1;
                }
            });
            // console.log(length+" "+totalchecked);
            // Checked unchecked checkbox
            if (totalchecked == length) {
                $("#checkAll").prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }
        }


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
