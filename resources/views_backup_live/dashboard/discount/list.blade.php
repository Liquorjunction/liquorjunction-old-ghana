@extends('dashboard.layouts.master')
@section('title', 'Cart Discount | Admin Panel')
@section('content')
    @include('sweetalert::alert')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">

    <style>
        .model {
            z-index: 1050 !important;
        }

        .model-backdrop {
            z-index: 1040 !important;
        }

        table.dataTable tbody td:last-child {
            padding: 8px 8px !important;
        }

        #option_width {
            width: 160px !important;
        }

        .btn {
            padding: 7px 10px;
        }
    </style>


    <div class="loader" id="loader"></div>
    <div class="padding website-label">
        <div class="success_message" style="margin-bottom: 10px;"></div>
        <div id="success_file_popup" style="margin-bottom: 10px;"></div>
        <div class="box">

            <div class="box-header dker">
                <h3>Cart Discount</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                    <span>Cart Discount</span>
                </small>
            </div>

            <div class="box-tool">
                <ul class="nav">

                    <li class="nav-item inline">
                        <a class="btn btn-fw primary" data-toggle="modal" data-target="#myModal" data-backdrop="static"
                            data-keyboard="false">
                            <i class="material-icons">&#xe02e;</i>
                            &nbsp; New Discount
                        </a>
                    </li>

                </ul>
            </div>

            {{ Form::open(['route' => 'discountUpdateAll', 'method' => 'post', 'id' => 'updateAll']) }}
            <div class="table-responsive">
                <table class="table table-bordered m-a-0" id="label">
                    <thead class="dker">
                        <tr>
                            <th id="discount">Discount Type</th>
                            <th id="amount">Discount Amount/ Percentage(%)</th>
                            <th id="minimum">Minimum Amount</th>
                            <th id="upto">Discount Up to Amount</th>
                            <th id="created">Created On</th>
                            <th id="expiry">Expiry Date</th>
                            <th>{{ __('backend.status') }}</th>
                            <th id="option_width">{{ __('backend.options') }}</th>
                        </tr>
                    </thead>
                    <tbody id="bannerTable">
                    </tbody>
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
                    </div>


                    <div class="col-sm-6 text-right text-center-xs">

                    </div>
                </div>
            </footer>
            {{ Form::close() }}

        </div>
    </div>


    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Discount</h4>
                </div>
                <form class="cmxform" id="discountForm" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Minimum Amount<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number" name="min_amount" id="min_amount" class="form-control" placeholder="100" value="{{ old('min_amount') }}" required>
                                <span style="color: red;display: none;" id="errorMsgMin" class='validate'></span>
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label class="col-sm-4 form-control-label">Maximum Amount<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-8">
                                <input type="number"  name="max_amount"  id="max_amount" class="form-control"
                                 placeholder="200"
                                    value="{{ old('max_amount') }}" required>
                                <span style="color: red;display: none;" id="errorMsgMax" class='validate'></span>
                            </div>
                        </div> --}}


                        <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Discount Type<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="flat" {{ old('discount_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                                </select>
                                <span style="color: red;display: none;" id="errorMsgType" class='validate'></span>
                            </div>
                        </div>
                      
                        <div class="form-group row" id="discountAmountDiv" style="display: none;">
                            <label class="col-sm-5 form-control-label">Discount Amount<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number" name="dis_amount" id="dis_amount" class="form-control" placeholder="100" value="{{ old('dis_amount') }}" required>
                                <span style="color: red;display: none;" id="errorMsgAmount" class='validate'></span>
                            </div>
                        </div>
                        <div class="form-group row" id="discountPercentageDiv">
                            <label class="col-sm-5 form-control-label">Discount Percentage(%)<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number" name="dis_percentage" id="dis_percentage" class="form-control" placeholder="100" value="{{ old('dis_percentage') }}" required>
                                <span style="color: red;display: none;" id="errorMsgPercentage" class='validate'></span>
                            </div>
                        </div>

                        <div class="form-group row"  id="discountUptoDiv">
                            <label class="col-sm-5 form-control-label">Discount Up to Amount<span class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="number" name="upto_amount"  id="upto_amount" class="form-control" placeholder="200" value="{{ old('upto_amount') }}" required>
                                <span style="color: red;display: none;" id="errorMsgUpto" class='validate'></span>
                            </div>
                        </div>
                       

                        <div class="form-group row">
                            <label class="col-sm-5 form-control-label">Expiry Date<span
                                    class="valid_field">*</span></label>
                            <div class="col-sm-7">
                                <input type="date"  name="expiry_date"  id="expiry_date" class="form-control"
                                 placeholder="Expiry Date"
                                    value="{{ old('expiry_date') }}" required>
                                <span style="color: red;display: none;" id="errorMsgExpiry" class='validate'></span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default btn btn-primary"><i class="material-icons">&#xe31b;</i> Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
                    </div>
                    {{-- {{ Form::close() }} --}}
                </form>
            </div>

        </div>
    </div>

    <div class="modal fade" id="editDiscount" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content add-discount-data">
            </div>
        </div>
    </div>

    <div class="modal fade" id="showDiscount" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content show-discount-data">
            </div>
        </div>
    </div>

@endsection

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function(e) {
            var discountform = $("#discountForm").validate({
            
                rules: {
                    discount_type: {
                        required: true,
                    },
                    dis_amount: {
                        required: function () {
                            return $("#dis_percentage").val() === "";
                        },
                        number: true,
                        min: 0
                    },
                    dis_percentage: {
                        required: function () {
                            return $("#dis_amount").val() === "";
                        },
                        number: true,
                        min: 0
                    },
                    min_amount: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    upto_amount:{
                        required: function () {
                            return $("#dis_amount").val() === "";
                        },
                        number: true,
                        min: 0 
                    },
                    expiry_date: {
                        required: true,
                        date: true,
                        futureDate: true
                    },
                },
                messages: {
                    discount_type: {
                        required: "Discount Type Field is required.",
                    },
                    dis_amount: {
                        required: "Discount Amount Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    dis_percentage: {
                        required: "Discount Percentage Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    min_amount: {
                        required: "Minimum Amount Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    upto_amount: {
                        required: "Discount Up to Amount Field is required.",
                        number: "Please enter a valid number.",
                        min: "Amount must be greater than or equal to 0."
                    },
                    expiry_date: {
                        required: "Expiry Date Field is required.",
                        date: "Enter a valid date.",
                        futureDate: "Expiry date must be in the future."
                    },
                },
                submitHandler: function() {
                    var form_data = new FormData($('#discountForm')[0]);
                    action_url = "{{ route('discountstore') }}";
                    var csrf = "{{ csrf_token() }}";
                    $.ajax({
                        url: action_url,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
                        processData: false,
                        contentType: false,
                        type: "POST",
                        dataType: 'json',
                        beforeSend: function() {
                            $('.loader').css("visibility", "visible");
                        },
                        success: function(data) {
                            if (data.success) {
                                window.location.href = "{{ route('discount') }}";
                            }
                        },
                        error: function(errors) {
                            $('.loader').css("visibility", "hidden");
                            var erroJson = JSON.parse(errors.responseText);
                            for (var err in erroJson) {
                                console.log(erroJson);
                                for (var errstr of erroJson[err])
                                $("span#errorMessage").css("display", "block");
                                $("span#errorMsgTitle").css("display", "block");
                                $("span#errorMsgTitle").html(erroJson.title);

                            }
                        }
                    });
                }
            });



            $.validator.addMethod("futureDate", function(value, element) {
                var now = new Date();
                var inputDate = new Date(value);
                return inputDate > now;
            }, "Date must be in the future.");

            $('#myModal').on('hidden.bs.modal', function() {
                discountform.resetForm();
                $('#myModal form')[0].reset();
            })

    });

    $(document).on('click', '.edit-discount', function(e) {
            $("#editDiscount").modal({
                backdrop: false
            });
            var discount_id = $(this).attr('data-id');
            $(document).find('#editDiscount').find(".add-discount-data").empty();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('discountedit') }}",
                type: 'POST',
                data: 'id=' + discount_id,
                success: function(response) {
                    $(document).find('#editDiscount').find(".add-discount-data").append(response.html);
                    $('.selectpicker').selectpicker();
                    $(document).find('#editDiscount').modal('show');
                },
                error: function(response) {

                    alert(response.responseText);
                }
            });
    });


    $(document).on('click', '.show-discount', function(e) {
        $("#showDiscount").modal({
            backdrop: false
        });
        var discount_id = $(this).attr('data-id');
        $(document).find('#showDiscount').find(".show-discount-data").empty();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('discount.show') }}",
            type: 'POST',
            data: 'id=' + discount_id,
            success: function(response) {
                $(document).find('#showDiscount').find(".show-discount-data").append(response.html);
                $(document).find('#showDiscount').modal('show');
            },
            error: function(response) {
                alert(response.responseText);
            }
        });
    });
    
    $(document).on('click', '.status_active', function(e) {
        var discount_id = $(this).attr('data-id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('discount.status_active')}}",
            type: 'POST',
            data: 'id='+discount_id,
            beforeSend: function(){
                $('.loader').css("visibility", "visible");
            },
            success: function (response) {
                $('.loader').css("visibility", "visible");
                window.location.href = "{{ route('discount')}}";
            },
            error: function (response) {
            alert(response.responseText);
        }
        });
    }); 

    $(document).on('click', '.status_inactive', function(e) {
        var discount_id = $(this).attr('data-id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('discount.status_inactive')}}",
            type: 'POST',
            data: 'id='+discount_id,
            beforeSend: function(){
                $('.loader').css("visibility", "visible");
            },
            success: function (response) {
                $('.loader').css("visibility", "visible");
                window.location.href = "{{ route('discount')}}";
            },
            error: function (response) {
            alert(response.responseText);
        }
    });
    }); 

</script>

<script type="text/javascript">
        function isNumberKey(evt) {
                var keyCode = (evt.which) ? evt.which : evt.keyCode;
                if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

                    return false;
                return true;
        }

    
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            load_data();

            function load_data() {
                var action_url = "{!! route('discount.anyData') !!}";

                $('#label').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ordering: true,
                    ajax: {
                        url: action_url,
                        type: 'POST'
                    },
                    columns: [
                        { data: 'discount', name: 'discount_type' },
                        { data: 'discount_amount', name: 'dis_amount' },
                        { data: 'minimum', name: 'min_amount' },
                        { data: 'upto', name: 'upto_amount' },
                        { data: 'created', name: 'created_at' },
                        { data: 'expiry', name: 'expiry_date' },
                        { data: 'status', name: 'status' },
                        { data: 'options', orderable: false, searchable: false }
                    ],

                    order: [[0, 'DESC']] 
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
                    if (type == 0) {
                        msg = "Are you sure you want to deactivate this Discount?";
                    } else if (type == 1) {
                        msg = "Are you sure you want to activate this Discount?";
                    } else {
                        msg = "Are you sure you want to delete this Discount?";
                    }

                    $(document).find('#default_confirm').modal('show');
                    $(document).find('#default_confirm').find('.dynamic_message').text(msg);
                    var join_selected_values = allVals.join(",");
                    $(document).find('#default_confirm').find('.checkbox_data').val(join_selected_values);
                    $(document).find('#default_confirm').find('.checkbox_type').val(type);

                }

            }
        });
        
        $(document).on('click', '.yes_click', function(e) {
            var join_selected_values = $(document).find('#default_confirm').find('.checkbox_data').val();
            var type = $(document).find('#default_confirm').find('.checkbox_type').val();
            var csrf = "{{ csrf_token() }}";
            ajaxUpdateAll(csrf, join_selected_values, type);
        });

         $(document).on('click', '.delete-discount', function(e) {
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

        function ajaxUpdateAll(csrf, join_selected_values, type) {
            $.ajax({
                url: "{{ route('discountUpdateAll') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                data: 'ids=' + join_selected_values + '&status=' + type,
                beforeSend: function() {
                    $('.loader').css("visibility", "visible");
                },
                success: function(data) {

                    if (data.success == true) {
                        $('.loader').css("visibility", "hidden");
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
                        $('.loader').css("visibility", "hidden");
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
        
        function messages(classname, msg) {
            return '<div class="alert ' + classname +
                ' m-b-0"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' +
                msg + '</div>';
        }

        setTimeout(function() {
            $('#topmsgall').hide();
        }, 5000);

        function isNumberBlock(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode != 43 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;
            return true;
        }

</script>

<script>
         $(document).ready(function() {
            toggleFieldsBasedOnDiscountType();

        $('#discount_type').change(function() {
            toggleFieldsBasedOnDiscountType();
        });

        function toggleFieldsBasedOnDiscountType() {
            var discountType = $('#discount_type').val();

            if (discountType === 'percentage') {
                document.getElementById('discountPercentageDiv').style.display="block"
                document.getElementById('discountUptoDiv').style.display="block"
                document.getElementById('discountAmountDiv').style.display="none"

            } else if (discountType === 'flat') {
                document.getElementById('discountPercentageDiv').style.display="none"
                document.getElementById('discountUptoDiv').style.display="none"
                document.getElementById('discountAmountDiv').style.display="block"
            }
        }
    });
</script>

@endpush
