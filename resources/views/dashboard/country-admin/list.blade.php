@extends('dashboard.layouts.master')
@section('title', 'Country Admin | Admin Panel')
@section('content')
@include('sweetalert::alert')
<style type="text/css">
    /*.card-body2{
         margin: -35px;
        margin-left: 10px;
       }*/
    .clear_button {
        margin-top: 30px;
    }
    .manage-space {
        white-space: nowrap;
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<div class="loader" id="loader"></div>
<div class="padding website-label">
    <div class="success_message" style="margin-bottom: 10px;"></div>
    <div id="success_file_popup" style="margin-bottom: 10px;"></div>
    <div class="box">
        <div class="box-header dker">
            <h3>{{ __('backend.countryAdmin') }}</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <span>{{ __('backend.countryAdmin') }}</span>
            </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="btn btn-fw primary " id="test_id" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#myModal">
                        <i class="material-icons">&#xe02e;</i>
                        &nbsp; {{ __('backend.NewCountryAdmin') }}
                    </a>
                </li>
            </ul>
        </div>
        {{-- <div class="card-body">
                <div class="form-group">
                    <div class="col-md-3">
                        <label>Start Date</label>
                        <input type="text" class="form-control" name="startdate" id="startdate" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="col-md-3">
                        <label>End Date</label>
                        <input type="text" class="form-control" name="enddate" id="enddate" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="col-md-2 clear_button">
                        <label>&nbsp;</label>
                        <a onclick="location.reload();">
                            <button type="button" class="btn btn-danger mr-2 mb-5">Clear</button>
                        </a>
                    </div>
                </div>
            </div> --}}
        {{ Form::open(['route' => 'countryUpdateAll', 'method' => 'post', 'id' => 'updateAll']) }}
        <div class="bulk-action">
            <div></div>
            <div>
                <select name="action" id="action" class="form-control c-select w-sm inline v-middle" required>
                    <option value="no">{{ __('backend.bulkAction') }}</option>
                    <option value="1">{{ __('backend.activeSelected') }}</option>
                    <option value="0">{{ __('backend.blockSelected') }}</option>
                    <option value="2">{{ __('backend.deleteSelected') }}</option>
                </select>
                <button type="submit" id="submit_all" class="btn white">{{ __('backend.apply') }}</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered m-a-0 manage-space" id="label">
                <thead class="dker">
                    <tr>
                        <th class="width20 dker no-sort">
                            <label class="ui-check m-a-0">
                                <input id="checkAll" type="checkbox"><i></i>
                            </label>
                        </th>
                        <!-- <th>Id</th> -->
                        <th>{{ __('backend.name') }}</th>
                        <th>{{ __('backend.email') }}</th>
                        <th>{{ __('backend.phone') }}</th>
                        <th>{{ __('backend.country')}}</th>
                        <th>{{ __('backend.joindate')}}</th>
                        <th>{{ __('backend.status') }}</th>
                        <th>{{ __('backend.options') }}</th>
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
                                    <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">{{ __('backend.no') }}</button>
                                    <button type="submit" class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div>
                    </div>
                    <!-- / .modal -->

                    {{-- <select name="action" id="action" class="form-control c-select w-sm inline v-middle"
                                        required>
                                    <option value="">{{ __('backend.bulkAction') }}</option>
                    <option value="activate">{{ __('backend.activeSelected') }}</option>
                    <option value="block">{{ __('backend.blockSelected') }}</option>
                    <option value="delete">{{ __('backend.deleteSelected') }}</option>
                    </select>
                    <button type="submit" id="submit_all" class="btn white">{{ __('backend.apply') }}</button>
                    <button id="submit_show_msg" class="btn white" data-toggle="modal" style="display: none" data-target="#m-all" ui-toggle-class="bounce" ui-target="#animate">{{ __('backend.apply') }}
                    </button> --}}

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
                <h4 class="modal-title">{{ __('backend.AddCountryAdmin') }}</h4>
            </div>
            <form class="cmxform" id="subadminForm" method="post" action="" autocomplete="off">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-cms"style="padding-right: 16px !important">Full Name
                            <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="fullname" id="fullname" class="form-control" onkeypress="return isNumberKey(event)" placeholder="Enter Name" value="{{ old('fullname') }}">
                            <span class="help-block" style="color: red;" id="errorMsgName" class='validate'></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.phone') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter Phone number" value="{{ old('phone') }}">
                            <span style="color: red;" id="errorMsgPhone" class='validate'></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.email') !!} <span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="email" id="email" class="form-control" placeholder="Enter Email" value="{{ old('email') }}">

                            <span class="help-block" style="color: red;" id="errorMsgEmail" class='validate'></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 form-control-cms">{{__('backend.country')}} <span class="valid_field">*</span></div>
                        <div class="col-sm-10">
                            <select name="country_id" id="country_id" class="form-control">
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                <option value="{{$country->id}}" {{(old('country_id', @$country->country_id ?: "") == $country->id) ? 'selected' : ''}}>
                                    {{ucfirst($country->name)}}
                                </option>
                                @endforeach
                            </select>
                            <span class="help-block" style="color: red;" id="errorMsgCountry" class='validate'></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Profile Photo<span class="valid_field">*</span></label>
                        <div class="col-sm-10">
                            <img id="blah" height="100" width="100" src="{{ asset('assets/dashboard/images/no_image_found.jpg') }}" alt="your image"  style="width:100px !important; height:100px !important;" />
                            <input type="file" name="profile" id="profile" class="form-control" style="border: none; margin-left: -13px;">
                            <span class="help-block" style="color: red;" id="errorMsgImage" class='validate'></span>

                            <div>
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    Choose image .png, .jpg, .jpeg files only.
                                </small>
                                <br>
                                <small>
                                    <i class="material-icons">&#xe8fd;</i>
                                    Recommended size 120(Width) x 180(Height).
                                </small>
                                <br>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default btn btn-primary" id="submit"><i class="material-icons">&#xe31b;</i> Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="material-icons">&#xe5cd;</i> Close</button>
                </div>
                {{ Form::close() }}
        </div>
    </div>
</div>

<div class="modal fade" id="editSubadmin" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content add-subadmin-data">
        </div>
    </div>
</div>

<div class="modal fade" id="showSubadmin" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content show-subadmin-data">
            </div>
    </div>
</div>

@endsection
@push('after-scripts')
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
<script>
// $(document).ready(function() {

//         $('#submit').on('click', function() {

//             $(this).attr('disabled',true);
//         });
//     });
// $('#subadminForm').submit(function(){
//     $('.loader').css("visibility", "visible");

// });
</script>
<script>
    function isNumberKey(evt) {
        //var e = evt || window.event;
        var keyCode = (evt.which) ? evt.which : evt.keyCode;
        if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

            return false;
        return true;

    }
    profile.onchange = evt => {
        const [file] = profile.files
        fileName = document.querySelector('#profile').value;
        extension = fileName.split('.').pop();
        document.querySelector('.output').textContent = extension;
        if (file) {
            blah.src = URL.createObjectURL(file)
        }
    }
    $(document).on('click', '.edit-subadmin', function(e) {
        $("#editSubadmin").modal({
            backdrop: false
        });
        var customer_id = $(this).attr('data-id');
        $(document).find('#editSubadmin').find(".add-subadmin-data").empty();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('country-admin.edit') }}",
            type: 'POST',
            data: 'customer_id=' + customer_id,
            success: function(response) {
                $(document).find('#editSubadmin').find(".add-subadmin-data").append(response.html);
                $(document).find('#editSubadmin').modal('show');
            },
            error: function(response) {
                alert(response.responseText);
            }
        });
    });

    $(document).on('click', '.show-subadmin', function(e) {
        $("#showSubadmin").modal({
            backdrop: false
        });
        var customer_id = $(this).attr('data-id');
        $(document).find('#showSubadmin').find(".show-subadmin-data").empty();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('country-admin.show') }}",
            type: 'POST',
            data: 'customer_id=' + customer_id,
            success: function(response) {
                $(document).find('#showSubadmin').find(".show-subadmin-data").append(response.html);
                $(document).find('#showSubadmin').modal('show');
            },
            error: function(response) {
                alert(response.responseText);
            }
        });
    });

    $(document).on('click', '.status_active', function(e) {
        var customer_id = $(this).attr('data-id');
        swal({
                title: "Deactive",
                text: "Are you sure you want to deactive this country admin?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#BF0A30",
                confirmButtonText: "Deactive",
                closeOnConfirm: false
            },
            function() {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('country-admin.status_active') }}",
                    type: 'POST',
                    data: 'customer_id=' + customer_id,
                    beforeSend: function() {
                        $('.loader').css("visibility", "visible");
                    },
                    success: function(response) {
                        $('.loader').css("visibility", "visible");
                        window.location.href = "{{ route('country-admin') }}";
                    },
                    error: function(response) {
                        alert(response.responseText);
                    }
                });
            });
    });

    $(document).on('click', '.status_inactive', function(e) {
        var customer_id = $(this).attr('data-id');
        // $(document).find('#editCustomer').find(".add-customer-data").empty();
        swal({
                title: "Active",
                text: "Are you sure you want to active this country admin?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#002868",
                confirmButtonText: "Active",
                closeOnConfirm: false
            },
            function() {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('country-admin.status_inactive') }}",
                    type: 'POST',
                    data: 'customer_id=' + customer_id,
                    beforeSend: function() {
                        // $(".loader").fadeIn();
                        $('.loader').css("visibility", "visible");
                    },
                    success: function(response) {
                        $('.loader').css("visibility", "visible");
                        // return false;
                        window.location.href = "{{ route('country-admin') }}";
                    },
                    error: function(response) {
                        alert(response.responseText);
                    }
                });
            });
    });

    $(document).ready(function() {

        // $(this).find('#subadminForm')[0].reset();
        // $('#subadminForm')[0].reset();
        $('#myModal form')[0].reset();
        $('#phone').on("input", function() {
            this.value = this.value.replace(/[^0-9\.]/g, '');
            $(this).val($(this).val().replace(/^\s+/g, ''));
        });

        var country_fields = $("#subadminForm").validate({
            submitHandler: function() {
                var form_data = new FormData($('#subadminForm')[0]);
                action_url = "{{ route('country-admin.store') }}";
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
                        // $(".loader").fadeIn();
                        // $('.loader').css("visibility", "visible");
                        $('.loader').css("visibility", "visible");
                    },
                    success: function(data) {
                        $('.loader').css("visibility", "hidden");
                        if (data.success) {
                            window.location.href = "{{ route('country-admin') }}";
                        }
                    },
                    error: function(errors) {
                        $('.loader').css("visibility", "hidden");
                        var errors = errors.responseJSON;
                        $("span#errorMsgName,span#errorMsgEmail,span#errorMsgPhone,span#errorMsgImage,span#errorMsgCountry").text('');
                        if (errors.fullname) {
                            $("span#errorMsgName").text(errors.fullname[0]);
                        }
                        if (errors.email) {
                            $("span#errorMsgEmail").text(errors.email[0]);
                        }
                        if (errors.phone) {
                            $("span#errorMsgPhone").text(errors.phone[0]);
                        }
                        if (errors.profile) {
                            $("span#errorMsgImage").text(errors.profile[0]);
                        }
                        if (errors.country_id) {
                            $("span#errorMsgCountry").text(errors.country_id[0]);
                        }
                       
                    }
                });
            }
        });
        $('#myModal').on('hidden.bs.modal', function() {
            $("span#errorMsgName,span#errorMsgEmail,span#errorMsgPhone,span#errorMsgImage,span#errorMsgCountry").text('');
            country_fields.resetForm();
            $('#myModal form')[0].reset();
        })
    });
</script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
        // $(document).ready(function () {
        //     $("#submit").click(function () {
        //         $(this).fadeOut();
        //     });
        // });
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

        function load_data(startdate, enddate) {

            var action_url = "{!! route('country-admin.anyData') !!} ";

            var dataTable = $('#label').DataTable({
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
                            // "startdate": $("#startdate").val().toLowerCase(),
                            // "enddate": $("#enddate").val().toLowerCase(),
                        });
                    }
                },
                columns: [{
                        data: 'checkbox',
                        orderable: false,
                        searchable: false

                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',

                    },
                    {
                        data: 'customer_email',
                        name: 'customer_email',
                    },
                    {
                        data: 'customer_phone',
                        name: 'customer_phone',
                    },
                    {
                        data: 'country',
                        name: 'country',
                    },
                    {
                        data: 'customer_join_date',
                        name: 'customer_join_date',
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

            // $('#startdate, #enddate').change(function() {
            //     {{-- let start_date = $('#startdate').val();
            //     let end_date = $('#enddate').val();
            //     load_data(start_date, end_date); --}}

            //     $("#export_start_date").val($("#startdate").val().toLowerCase());
            //     $("#export_end_date").val($("#enddate").val().toLowerCase());

            //     dataTable.draw();
            // });
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

    $("#startdate").datepicker({
        changeMonth: true,
        endDate: '+0d',
        changeYear: true,
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        orientation: "bottom",
        autoclose: true
    }).on('changeDate', function(selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#enddate').datepicker('setStartDate', minDate);
    });

    $("#enddate").datepicker({
        changeMonth: true,
        endDate: '+0d',
        changeYear: true,
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        orientation: "bottom",
        autoclose: true
    }).on('changeDate', function(selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#startdate').datepicker('setEndDate', minDate);
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
                    msg = "Are you sure you want to deactive this country admin?";
                } else if (type == 1) {
                    msg = "Are you sure you want to active this country admin?";
                } else {
                    msg = "Are you sure you want to delete this country admin?";
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
            url: "{{ route('countryAdminUpdateAll') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            data: 'ids=' + join_selected_values + '&status=' + type,
            beforeSend: function() {
                // $(".loader").fadeIn();
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
@endpush