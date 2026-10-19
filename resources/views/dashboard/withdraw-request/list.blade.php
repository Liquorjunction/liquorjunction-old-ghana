@extends('dashboard.layouts.master')
@section('title','Withdraw Request')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />

<div class="padding list-school">
    <div class="success_message" style="margin-bottom: 10px;"></div>
    <div id="success_file_popup" style="margin-bottom: 10px;"></div>
    <div class="box">

        <div class="box-header dker">
            <h3>Withdraw Request<h3>
                    <small>
                        <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                        <span href="javascript:void(0);">Withdraw Request</span>
                    </small>
        </div>

        <div class="box-tool">
                <ul class="nav">
                       
                            {{-- <li class="nav-item inline">
                                <a class="btn btn-fw primary" href="{{route('dance-class.create')}}">
                                    <i class="material-icons">&#xe02e;</i>
                                    &nbsp; {{ __('backend.new') }} Dance Class
                                </a>
                            </li> --}}
                       
                </ul>
            </div>

        <div class="row margin" style="">
            <div class="col-xs-6">

            </div>
            <div class="col-xs-6" style="display: none;">
                {{Form::open(['route'=>'filterrequestdate','method'=>'post','style="background :none !important;"','id'=>'filter_form'])}}
                <div class="bulk-action" style="float: right;">
                    <input type="text" class="form-control" style="color: #001645;font-weight:500;width: 200px;height: 8px;" value="{{ isset($filterdate)?$filterdate:old('date_filter') }}" placeholder="MM-DD-YYYY" name="date_filter" id="date_filter" />
                    <?php $report =  route('user-withdraw-request'); ?>
                    <a href="javascript:void(0)" style="margin-left: 10px;" id="filter"><button type="button" style="min-width: unset;" class="btn btn-fw primary primary mr-2">Filter</button></a>
                    <a style="margin-left: 7px;" href="{{$report}}" id="filter">
                        <button type="button" class="btn btn-default mr-2" style="min-width: unset;">Clear</button>
                    </a>

                    {{Form::close()}}
                    {{-- {{Form::open(['route'=>'userexport','method'=>'post','style="background :none !important;"','id'=>'userexport'])}} --}}

                    <input type="hidden" name="startdate" value="{{ isset($start) ? $start : ''}}" id="export_start_date">
                    <input type="hidden" name="enddate" value="{{ isset($end) ? $end : ''}}" id="export_end_date">
                    {{-- <div class="col-sm-12">
                        <a style="margin-top: -9px; height:35px; min-width: unset;" class="btn btn-fw primary export-form" href="javascript:void(0)">
                            Export
                        </a>
                    </div>

                    {{Form::close()}} --}}
                </div>
            </div>
        </div>








        {{-- <div class="box-tool">
                <ul class="nav">
                        <li class="nav-item inline">
                           <a class="btn btn-fw primary" href="{{ route('vehicle-type.create') }}">
        <i class="material-icons">&#xe02e;</i>
        &nbsp; {{ __('backend.new') }} Vehicle Type</a>
        </li>
        </ul>
    </div> --}}


    {{Form::open(['route'=>'withdraw-requestUpdateAll','method'=>'post','id'=>'updateAll'])}}
    <div class="bulk-action">
        <div></div>
        <div>
            <select name="action" id="action" class="form-control c-select w-sm inline v-middle" required>
                <option value="no">{{ __('backend.bulkAction') }}</option>
                <!-- <option value="0">Requested</option> -->
                <option value="1">Paid</option>
                <option value="2">Denied</option>
            </select>
            <button type="submit" id="submit_all" class="btn white">{{ __('backend.apply') }}</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered m-a-0" id="vehiclelist">
            <thead class="dker">
                <tr>
                    <th class="width20 dker no-sort">
                        <label class="ui-check m-a-0">
                            <input id="checkAll" type="checkbox"><i></i>
                        </label>
                    </th>
                    <th>Instructor Name</th>
                    <th>Request Date</th>
                    <th>Account Balance</th>
                    <th>Requested Amount</th>
                    {{-- <th>Payment Status</th> --}}
                    <th class="">{{ __('backend.status') }}</th>
                    <th class="">{{ __('backend.options') }}</th>
                </tr>
            </thead>
            <tbody>

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
                                <button type="button" class="btn dark-white p-x-md" data-dismiss="modal">{{ __('backend.no') }}</button>
                                <button type="submit" class="btn danger p-x-md">{{ __('backend.yes') }}</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div>
                </div>
                <!-- / .modal -->

            </div>

        </div>
    </footer>
    {{Form::close()}}

</div>
</div>
@endsection
@push("after-scripts")
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="{{ asset('assets/dashboard/js/jquery.dataTables.min.js') }}"></script>


<script type="text/javascript">
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        load_data();

        function load_data(from_date, to_date) {

            var action_url = "{!!  route('withdraw-request.data') !!} ";

            $('#vehiclelist').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: true,
                columnDefs: [{
                    'bSortable': false,
                    'aTargets': [0,6]
                }],
                ajax: {
                    url: action_url,
                    type: 'POST',
                    data: {
                        startdate: from_date,
                        enddate: to_date
                    }
                },
                columns: [{
                        data: 'checkbox',
                        orderable: false,
                        searchable: false

                    },
                    {
                        data: 'uname',
                        name: 'uname',
                    },
                    {
                        data: 'createddate',
                        name: 'createddate',
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                    }, 
                    {
                        data: 'request_status',
                        name: 'request_status',
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

        var flag = true;
        $('#filter').click(function() {
            var date = $('#date_filter').val();
            dateInterval = date.split(' - ');
            from_date = dateInterval[0];
           
            to_date = dateInterval[1];



            if (from_date != '' && to_date != '') {
                if (to_date < from_date) {
                    alert('Start date must be less than end date');
                    flag = false;
                } else {
                    flag = true;

                }
            } else {
                alert('Both Date is required');
                flag = false;
            }
            if (flag) {
                $('#vehiclelist').DataTable().destroy();

                $(document).find('#export_start_date').val(from_date);
                $(document).find('#export_end_date').val(to_date);
                load_data(from_date, to_date);

            }

        });

    });

    $(".export-form").click(function() {
        $('#userexport').submit();
    });

    $(document).ready(function() {
        if ($('.no-sort').hasClass('sorting_disabled')) {
            $('.no-sort').removeClass('sorting_asc')
        }
    });
    /* $('#school_admin').DataTable({
             'columnDefs': [{ 'orderable': false, 'targets': 0 }],
        });*/

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
    /*  $('#updateAll').submit(function(e){
          var numberOfChecked = $('input:checkbox:checked').length;
          if(numberOfChecked == ''){
              alert("Please select row.");
              return false;
          } 
      });*/
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
                    msg = "Are you sure you want to requested status of this withdraw request?";
                } else if (type == 1) {
                    msg = "Are you sure you want to paid status of this withdraw request?";
                } else {
                    msg = "Are you sure you want to denied status of this withdraw request?";
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
        var csrf = "{{csrf_token()}}";
        ajaxUpdateAll(csrf, join_selected_values, type, action);
        $("#default_confirm").modal('hide');
    });

    $(document).on('click', '.has-popularvalue', function(){  
               
               var id = $(this).attr("data-id");
               var value = $(this).attr("value");
              
               var allVals = [];
               allVals.push(id);
               var join_selected_values = allVals.join(",");
               var popular_category = ''; 
              // var join_selected_values = $(document).find('#default_confirm').find('.checkbox_data').val();
              // alert(join_selected_values);
               var csrf = "{{csrf_token()}}";
               if($('.has-popularvalue').is(':checked') == true)
               {
                   popular_category = 1;
               }
               else
               {
                   popular_category = 0;
               }
              
               // $.ajax({
               //     url: "{{ route('popularClassUpdateAll')}}",
               //     type: 'POST',
               //     headers: {
               //         'X-CSRF-TOKEN': csrf
               //     },
               //     data: 'ids=' + join_selected_values + '&is_popular_Category=' + popular_category,
               //     success: function(data) {
       
               //         if (data.success == true) {
               //             alert("success");
               //             location.reload();
               //             // $('#success_file_popup').append(messages('alert-success', data.msg));
               //             // setTimeout(function() {
               //             //     $('#success_file_popup').empty();
               //             // }, 5000);
       
       
               //             // $(document).find('#default_confirm').modal('hide');
               //             // var tabe = $('#vehiclelist').DataTable();
               //             // $(document).find('#action').prop('selectedIndex', 0);
               //             // tabe.ajax.reload(null, false);
               //             // $("#checkAll").prop('checked', false);
       
               //         } else {
               //             // $('#success_file_popup').append(messages('alert-danger', data.error));
       
               //             // setTimeout(function() {
               //             //     $('#success_file_popup').empty();
               //             // }, 5000);
               //             alert("error");
               //         }
               //     },
               //     error: function(data) {
               //         alert(data.responseText);
               //     }
               // });
    });

    function ajaxUpdateAll(csrf, join_selected_values, type) {
        $.ajax({
            url: "{{ route('withdraw-requestUpdateAll')}}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            data: 'ids=' + join_selected_values + '&request_status=' + type,
            success: function(data) {

                if (data.success == true) {
                    $('#success_file_popup').append(messages('alert-success', data.msg));
                    setTimeout(function() {
                        $('#success_file_popup').empty();
                    }, 5000);


                    $(document).find('#default_confirm').modal('hide');
                    var tabe = $('#vehiclelist').DataTable();
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
        return '<div class="alert ' + classname + ' m-b-0"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>' + msg + '</div>';
    }
    setTimeout(function() {
        $('#topmsgall').hide();
    }, 5000);

    $(function() {
        let dateInterval = getQueryParameter('date_filter');

        let start = "<?php echo ($start); ?> ";
        let end = "<?php echo ($end); ?> ";

        if (dateInterval) {
            dateInterval = dateInterval.split(' - ');
            start = dateInterval[0];
            end = dateInterval[1];
        }
        $('#date_filter').daterangepicker({
            "showDropdowns": true,
            "showWeekNumbers": true,
            "alwaysShowCalendars": true,
            autoUpdateInput: false,
            startDate: start,
            endDate: end,
            locale: {
                format: 'MM-DD-YYYY',
                cancelLabel: 'Clear',
                firstDay: 1,

            },

        });

        $('#date_filter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM-DD-YYYY') + ' - ' + picker.endDate.format('MM-DD-YYYY'));
        });

    });

    function getQueryParameter(name) {
        const url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
</script>


@endpush