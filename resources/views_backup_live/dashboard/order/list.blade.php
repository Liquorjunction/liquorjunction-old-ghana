@extends('dashboard.layouts.master')
@section('title', 'Order | Admin Panel')
@section('content')
    @include('sweetalert::alert')
    <style type="text/css">
        .pointer_button {
            pointer-events: none !important;
            height: 33px !important;
            width: 82px !important;
        }

        .clear_button {
            margin-top: 30px;
        }

        .status-dropdown {
            position: relative;
            display: inline-block;
        }

        .status-dropdown-button {
            /* background-color: #4CAF50;
                    color: white;
                    padding: 5px 10px;
                    border: none;
                    cursor: pointer; */
        }

        .status-dropdown-content {
            display: none;
            left: -20px;
            position: absolute;
            background-color: #f9f9f9;
            /* min-width: 160px; */
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .status-dropdown-item {
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            color: #333;
        }

        .status-dropdown-item:hover {
            background-color: #ddd;
        }

        .dker {
            padding-top: 0px !important;
            padding-bottom: 0px !important;
        }

        .manage-space {
            white-space: nowrap;
        }
    </style>
    <?php
    // $user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
    ?>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <div class="loader" id="loader"></div>
    <div class="padding website-label">
        <div class="success_message" style="margin-bottom: 10px;"></div>
        <div id="success_file_popup" style="margin-bottom: 10px;"></div>
        <div class="box">
            <div class="box-header dker">
                <h3>{{ __('backend.OrderManagement') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>{{ __('backend.OrderManagement') }}</span>
                </small>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="col-md-3">
                        <label>Order Type</label>
                        <select name="order_type_status" id="order_type_status" class="form-control">
                            <option value="">Select Order Type</option>
                            <option value="1">Online</option>
                            <option value="2">Pickup Order</option>
                        </select>
                    </div>
                    <div class="col-md-2 clear_button">
                        <label>&nbsp;</label>
                        <a onclick="location.reload();">
                            <button type="button" class="btn btn-danger mr-2 mb-5">Clear</button>
                        </a>
                    </div>
                </div>
            </div>
            {{ Form::open(['route' => 'orderstatusUpdateAll', 'method' => 'post', 'id' => 'updateAll']) }}
            @php
                $check_updation_permission = @Helper::GetRolePermission(Auth::user()->user_type, 28, 'update');
                $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 28, 'read');
            @endphp
            {{-- <div class="bulk-action">
            <div></div>
            @if (isset($check_updation_permission) && $check_updation_permission == true)
            <div>
                <select name="action" id="action" class="form-control c-select w-sm inline v-middle" required>
                    <option value="no">{{ __('backend.bulkAction') }}</option>
                    @if (isset($check_updation_permission) && $check_updation_permission == true)
                    <option value="1">{{ __('backend.pendingSelected') }}</option>
                    <option value="2">In Process</option>
                    <option value="3">Delivered</option>
                    <option value="4">{{ __('backend.cancleSelected') }}</option>
                    @endif
        </select>
        <button style="" type="submit" id="submit_all" class="btn white">{{ __('backend.apply') }}</button>
    </div>
    @endif
</div> --}}
            <div class="table-responsive" style="margin-top: 50px;">
                <table class="table table-bordered m-a-0 manage-space" id="label">
                    <thead class="dker">
                        <tr>
                            <th class="width20 dker no-sort">
                                <label class="ui-check m-a-0">
                                    <input id="checkAll" type="checkbox"><i></i>
                                </label>
                            </th>
                            <!-- <th>Id</th> -->
                            <th id="">{{ __('backend.order_id') }}</th>
                            <th id="">Customer {{ __('backend.name') }}</th>
                            <th id="">Customer Phone Number</th>
                            <th id="">Country</th>
                            <th id="">{{ __('backend.order_type') }}</th>
                            <th id="">Paid Amount</th>
                            <th id="">Promo Code</th>
                            <th id="">Order Date</th>
                            <th id="">Delivery Status</th>
                            <th id="">Cancelled By</th>
                            <th id="">Payment Type</th>
                            <th id="">Payment Status</th>
                            <!-- <th>Order {{ __('backend.status') }}</th> -->
                            <th id="option_width">{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                    <tbody id="bannerTable"> </tbody>
                </table>
            </div>
            <footer class="dker p-a">
                <div class="row">
                    <div class="col-sm-3 hidden-xs">
                        <!-- .modal -->
                        <div id="m-all" class="modal fade" data-backdrop="true">
                            <div class="modal-dialog" id="animate">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('backend.confirmation') }}</h5>
                                    </div>
                                    <div class="modal-body text-center p-lg">
                                        <p>
                                            {{ __('backend.confirmationDeleteMsg') }}
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn dark-white p-x-md"
                                            data-dismiss="modal">{{ __('backend.no') }}</button>
                                        <button type="submit" class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div>

                        </div>
                        <!-- / .modal -->

                        {{-- <select name="action" id="action" class="form-control c-select w-sm inline v-middle"
                                        required>
                                    <option value="">{{ __('backend.bulkAction') }}</option>
            <option value="activate">{{ __('backend.pendingSelected') }}</option>
            <option value="block">{{ __('backend.complatedSelected') }}</option>
            <option value="delete">{{ __('backend.deleteSelected') }}</option>
            </select>
            <button type="submit" id="submit_all" class="btn white">{{ __('backend.apply') }}</button>
            <button id="submit_show_msg" class="btn white" data-toggle="modal" style="display: none" data-target="#m-all" ui-toggle-class="bounce" ui-target="#animate">{{ __('backend.apply') }}
            </button> --}}


                        <div id="updateOrderStatus" class="modal fade" data-backdrop="true" tabindex="-1" role="dialog"
                            aria-labelledby="updateOrderStatusModal" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" id="updateOrderStatusanimate">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('backend.confirmation') }} <span
                                                class="orderid-show"></span></h5>
                                    </div>
                                    <div class="modal-body text-center p-lg">
                                        <p class="confirm-message">
                                            Are you sure you want to change status of this order to Dispatched?
                                        </p>
                                        <textarea id="note" name="note" class="form-control" style="display: none;" placeholder="Enter note here..." rows="3"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn dark-white p-x-md"
                                            data-dismiss="modal">{{ __('backend.no') }}</button>
                                        <input type="hidden" id="change_order_id" value="">
                                        {{-- <input type="hidden" id="change_note" value=""> --}}
                                        <input type="hidden" id="change_order_status" value="">
                                        <input type="hidden" id="customer_mobile" value="">
                                        <input type="hidden" id="customer_phonecode" value="">
                                        <input type="hidden" id="user_id" value="">
                                        <button type="button" id="update_order_status_submission"
                                            class="btn danger p-x-md" style="background: #fbb516 !important;  color: black !important;  border: 1px solid #fbb516 !important;">{{ __('backend.yes') }}</button>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-6 text-right text-center-xs">

                    </div>
                </div>
            </footer>
            {{ Form::close() }}
        </div>
    </div>
@endsection
@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        var show = "";
        var sort = "";
        var check = "";
    </script>
    <?php
if (isset($check_updation_permission) && $check_updation_permission == true) {
?>
    <script>
        var show = true;
        var check = true;
        var sort = [0, 10, 11];
    </script>
    <?php } else if ($check_view_permission == true) {
?>
    <script>
        var show = true;
        var check = false;
        var sort = [0, 10, 11];
    </script>
    <?php } else { ?>
    <script>
        var show = false;
        var check = false;
        var sort = [0, 10];
    </script>
    <?php } ?>
    <script type="text/javascript">
        function isNumberKey(evt) {
            //var e = evt || window.event;
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

                return false;
            return true;

        }
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            load_data();

            function load_data() {

                var action_url = "{!! route('adminorder.anyData') !!} ";

                var dataTable = $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    // columnDefs: [{
                    //     'bSortable': false,
                    //     'aTargets': sort
                    // }],
                    ajax: {
                        url: action_url,
                        type: 'POST',
                        data: function(d) {
                            return $.extend({}, d, {
                                "order_type_status": $("#order_type_status").val()
                                    .toLowerCase(),
                            });
                        }
                    },
                    columns: [{
                            data: 'checkbox',
                            orderable: false,
                            searchable: false,
                            visible: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name',

                        },
                        {
                            data: 'customer_mobile',
                            name: 'customer_mobile',

                        },
                        {
                            data: 'country_name',
                            name: 'country_name',

                        },
                        {
                            data: 'order_type',
                            name: 'order_type',
                        },
                        {
                            data: 'grand_total_amount',
                            name: 'grand_total_amount',
                        },
                        {
                            data: 'promo_code',
                            name: 'promo_code',
                        },
                        {
                            data: 'order_date',
                            name: 'order_date',
                        },
                        {
                            data: 'delivery_status',
                            name: 'delivery_status',
                        },
                        {
                            data: 'cancelled_by',
                            name: 'cancelled_by',
                        },
                        {
                            data: 'payment_type',
                            name: 'payment_type',
                        }, {
                            data: 'payment_status',
                            name: 'payment_status',
                        },

                        {
                            data: 'options',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: ['0', 'DESC']
                });
                $('#order_type_status').change(function() {

                    $("#export_order_type_status").val($("#order_type_status").val().toLowerCase());
                    //$("#export_is_admin_approve").val($("#is_admin_approve").val().toLowerCase());
                    dataTable.draw();
                });
            }

        });


        $(document).ready(function() {
            if ($('.no-sort').hasClass('sorting_disabled')) {
                $('.no-sort').removeClass('sorting_asc')
            }
        });


        $("#submit_show_msg").click(function() {
            var numberOfChecked = $('input:checkbox:checked').length;
            if (numberOfChecked == '') {
                alert("Please select row.");
                return false;
            }
        });


        $("#checkAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });



        $("#filter_btn").click(function() {
            $("#filter_div").slideToggle();
        });

        $("#find_q").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#doctorTypeTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $(document).on('submit', '#updateAll', function(e) {
            e.preventDefault();
            var allVals = [];
            var check = false;

            var select_row = "{{ __('backend.select_row') }}";
            var select_status = "{{ __('backend.select_status') }}";

            var type = $(document).find('#action').val();
            if (type == 'no') {
                $(document).find('#alert_confirm').modal('show');
                $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_status);

            } else {
                $(".has-value:checked").each(function() {
                    var idvalue = $(this).attr('data-id');
                    if (typeof idvalue === "undefined") {

                    } else {
                        allVals.push(idvalue);
                    }
                });

                if (allVals.length <= 0) {
                    $(document).find('#alert_confirm').modal('show');
                    $(document).find('#alert_confirm').find('.alert_dynamic_message').text(select_row);
                } else {
                    var msg = "";
                    if (type == 1) {
                        msg = "Are you sure you want to pending this order?";
                    } else if (type == 2) {
                        msg = "Are you sure you want to in process1 this order?";
                    } else if (type == 5) {
                        msg = "Are you sure you want to ready to pick up this order?";
                    } else if (type == 6) {
                        msg = "Are you sure you want to out for delivery this order?";
                    } else if (type == 3) {
                        msg = "Are you sure you want to delivered this order?";
                    } else if (type == 4) {
                        msg = "Are you sure you want to cancel this order?";
                    } else {
                        msg = "Are you sure you want to this order status?";
                    }

                    $(document).find('#default_confirm').modal('show');
                    $(document).find('#default_confirm').find('.dynamic_message').text(msg);
                    var join_selected_values = allVals.join(",");
                    $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
                    $(document).find('#default_confirm').find('.checkbox_type').val(type);

                }

            }
        });
        $(document).on('click', '.delete-school', function(e) {
            e.preventDefault();
            var package_id = $(this).attr('data-id');
            var allVals = [];
            allVals.push(package_id);
            var type = 2;
            var msg = "Are you sure you want to delete?";

            $(document).find('#default_confirm').modal('show');
            $(document).find('#default_confirm').find('.dynamic_message').text(msg);
            var join_selected_values = allVals.join(",");
            $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
            $(document).find('#default_confirm').find('.checkbox_type').val(type);
        });

        $(document).on('click', '.yes_click', function(e) {
            var join_selected_values = $(document).find('#default_confirm').find('.checkbox_data').val();
            var type = $(document).find('#default_confirm').find('.checkbox_type').val();
            var csrf = "{{ csrf_token() }}";
            ajaxUpdateAll(csrf, join_selected_values, type, action);
        });

        function ajaxUpdateAll(csrf, join_selected_values, type) {
            $.ajax({
                url: "{{ route('orderstatusUpdateAll') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                data: 'ids=' + join_selected_values + '&status=' + type,
                success: function(data) {

                    if (data.success == true) {
                        $('#success_file_popup').append(messages('alert-success', data.msg));
                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);


                        $(document).find('#default_confirm').modal('hide');
                        var tabe = $('#label').DataTable();
                        $(document).find('#action').prop('selectedIndex', 0);
                        tabe.ajax.reload(null, false);
                        $("#checkAll").prop('checked', false);

                    } else {
                        $('#success_file_popup').append(messages('alert-danger', data.error));

                        setTimeout(function() {
                            $('#success_file_popup').empty();
                        }, 5000);
                    }
                },
                error: function(data) {
                    alert(data.responseText);
                }
            });
        }
        $("#action").change(function() {
            if (this.value == "delete") {
                /*var numberOfChecked = $('input:checkbox:checked').length;
                if(numberOfChecked == ''){
                    alert("Please select row.");
                    return false;
                } */
                $("#submit_all").css("display", "none");
                $("#submit_show_msg").css("display", "inline-block");
            } else {
                $("#submit_all").css("display", "inline-block");
                $("#submit_show_msg").css("display", "none");
            }
        });

        function messages(classname, msg) {
            return '<div class="alert ' + classname +
                ' m-b-0"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' +
                msg + '</div>';
        }
        setTimeout(function() {
            $('#topmsgall').hide();
        }, 5000);
    </script>
    <script>
        function toggleDropdown(orderId) {
            var dropdown = document.getElementById("statusDropdown" + orderId);
            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                dropdown.style.display = "block";
            }
        }

        function updateStatus(element, status, status_id, customer_mobile, customer_phonecode, user_id , note) {
            // Replace this with your logic to update the order status for the specified order ID
            // alert("Order " + orderId + " status updated to: " + status);
            let orderID_cap = $(element).data('orderid');
            // let note = $(element).data('note');
            // console.log(note);
            let order_id = $(element).data('id');
            $("#change_order_id").val(order_id);
            // let note = $("#note").val();
            $("#change_order_status").val(status_id);
            $('#customer_mobile').val(customer_mobile);
            $('#customer_phonecode').val(customer_phonecode);
            $('#user_id').val(user_id);

            let confirm_modal = $("#updateOrderStatus");
            confirm_modal.find('.orderid-show').text('#' + orderID_cap);
            confirm_modal.find('.confirm-message').text(
                `Are you sure you want to change status of this order to ${status}?`);

            if (status_id == 2) { // Assuming "2" is the ID for "Accepted"
                $('#note').show();
                
            } else {
                // alert(1);
                $('#note').hide();
            }
            // alert(3);
            $('#note').val('');
            confirm_modal.modal('show');
        }

        $('#updateOrderStatus').on('hidden.bs.modal', function() {

            // alert(4);
            $('#note').val('');
        });

        $(document).on("click", function(event) {
            var $trigger = $(".status-dropdown");

            if ($trigger !== event.target && !$trigger.has(event.target).length) {
                $(".status-dropdown-content").hide();
            }
        });

        $('#update_order_status_submission').click(function() {
            let order_id = $("#change_order_id").val();
            let note = $("#note").val();
            // console.log(note) 
            let order_status = $("#change_order_status").val();
            let mobile_number = $('#customer_mobile').val();
            let phone_code = $('#customer_phonecode').val();
            // alert(phone_code);
            let user_id = $('#user_id').val();

            // alert(mobile_number);
            // let country_code = "91";

            let customer_mobile_number = "+" + phone_code + mobile_number;
            // alert(customer_mobile_number);
            if (order_id && order_status)  {
                $.ajax({
                    url: "{{ route('adminorder.updateOrderStatus') }}",
                    type: 'POST',
                    data: {
                        order_id: order_id,
                        order_status: order_status,
                        note: note
                    },
                    success: function(result) {
                        $('#updateOrderStatus').modal('hide');
                        // $("html, body").animate({ scrollTop: "0" });
                        $('#order_type_status').trigger('change');
                        if (result.success == true) {
                            $('#success_file_popup').append(messages('alert-success', result.msg));
                            setTimeout(function() {
                                $('#success_file_popup').empty();
                            }, 5000);
                            if (order_status == 2 || order_status == 3 || order_status == 5 ||
                                order_status == 6) {
                                // alert(customer_mobile_number);
                                if (order_status == 2) {
                                    var message = 'Order is Confirmed';
                                } else if (order_status == 5) {
                                    var message = 'Order is Ready to Pick Up';
                                } else if (order_status == 6) {
                                    var message = 'Order is Out Of Delivery';
                                } else {
                                    var message = 'Order is Delivered';
                                }
                                $.ajax({
                                    url: "{{ route('send-sms') }}",
                                    type: 'POST',
                                    data: {
                                        mobile_number: mobile_number,
                                        message: message
                                    },
                                    success: function(smsResult) {
                                        // Handle the result if needed
                                    },
                                    error: function(error) {
                                        // Handle the error if needed
                                    }
                                });
                            }
                        } else {
                            $('#success_file_popup').append(messages('alert-danger', result.msg));

                            setTimeout(function() {
                                $('#success_file_popup').empty();
                            }, 5000);
                        }
                    }
                });
            } else {
                $("#updateOrderStatus").modal('hide');
                alert('Something went wrong!!!');
                location.reload();
            }
        });
    </script>
@endpush
