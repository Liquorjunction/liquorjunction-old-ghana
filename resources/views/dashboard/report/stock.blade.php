@extends('dashboard.layouts.master')
@section('title','Stock Report')
@section('content')
<style>
    .school-report-manage .school-report-form .paging_simple_numbers span a {
        border: 1px solid #3699ff;
        color: #3699ff;
        background: #fff !important;
        padding: 2px 8px;
        border-radius: 5px;
        padding: 2px 8px;
        margin: 0 5px;
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />

<div class="padding school-report-manage list-school">
    <div class="box">

        <div class="box-header dker">
            <h3>Stock Report</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> /
                <span>Stock Report</span>
            </small>
        </div>

        <div class="row margin filter_message" style="margin-top: -50px;">
            <div class="col-xs-6">

            </div>
            <div class="col-xs-6">
                {{Form::open(['route'=>'report.stock-filter','style="background :none !important;"','method'=>'post','id'=>'filter_form'])}}
                <div class="bulk-action" style="float: right;">
                    <input type="text" class="form-cntrol" style="color: #001645;font-weight:500;width: 200px;height: 8px;" value="{{ isset($filterdate)?$filterdate:old('date_filter') }}" placeholder="MM-DD-YYYY" name="date_filter" id="date_filter" />
                    <span class="removeError" style="color: red; display: none;" id="span">Please select start date and end date</span>
                    <?php $report =  route('stock-report'); ?>
                    <a style="margin-left: 10px;" href="javascript:void(0)" id="filter"><button type="button" style="min-width: unset;" class="btn btn-fw primary primary mr-2">Filter</button></a>
                    <a style="margin-left: 10px;" href="{{$report}}" id="filter">
                        <button type="button" style="padding: 7px;" class="btn btn-default mr-2" style="min-width: unset;">Clear</button>
                    </a>
                    {{Form::close()}}
                    {{Form::open(['route'=>'report.stock-export-pdf','method'=>'post','style="background :none !important;"','id'=>'export'])}}

                    <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : ''}}" id="export_start_date">
                    <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : ''}}" id="export_end_date">
                    <div class="col-sm-12">
                        <a style="height:35px; min-width: unset; margin-right: -10px;" class="btn btn-fw primary export-form" href="javascript:void(0)">
                            Export in PDF
                        </a>
                    </div>
                    {{Form::close()}}
                    {{Form::open(['route'=>'report.stock-export','method'=>'post','style="background :none !important;"','id'=>'export1'])}}

                    <input type="hidden" name="startdate1" value="{{ isset($startdate) ? $startdate : ''}}" id="export_start_date1">
                    <input type="hidden" name="enddate1" value="{{ isset($enddate) ? $enddate : ''}}" id="export_end_date1">
                    <div class="col-sm-12">
                        <a style="height:35px; min-width: unset; margin-top: -11px;" class="btn btn-fw primary export-form1" href="javascript:void(0)">
                            Export in Excel
                        </a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
             <!-- <span class="removeError" style="color: red; display: none;" id="span">Please select start date and end date</span> -->
        </div>








        <div class="table-responsive school-report-form">
            <table class="table table-bordered m-a-0" id="report_admin">
                <thead class="dker">
                    <tr>
                        <th class="width20 dker no-sort" style="display: none;">
                        <label class="ui-check m-a-0">
                            <input id="checkAll" type="checkbox"><i></i>
                        </label>
                    </th>
                        <th>Class Name</th>
                        <th>Instructor Name</th>
                        <th>Purchase User</th>
                        <th>Purchase Date</th>
                        <th>Instructor Amount</th>
                        <th>Admin Commission Amount</th>
                        <th>Class Price</th>
                        <!-- <th>Total Amount</th> -->
                        <!-- <th>Total Course Purchase</th> -->
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
        <div class="white-space"></div>

    </div>
</div>
@endsection
@push("after-scripts")

<!--  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script> -->

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

        function load_data(startdate, enddate) {

            var action_url = "{!!  route('report.earning') !!} ";

            $('#report_admin').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: true,
                columnDefs: [{
                    'bSortable': false,
                    'aTargets': []
                }],
                ajax: {
                    url: action_url,
                    type: 'POST',
                    data: {
                        startdate: startdate,
                        enddate: enddate
                    }
                },
                columns: [
                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        visible: false
                    },
                    {
                        data: 'class_name',
                        name: 'class_name',
                    },
                    {
                        data: 'user_name',
                        name: 'user_name',
                    },
                    {
                        data: 'purchase_user',
                        name: 'purchase_user',
                    },
                    {
                        data: 'created',
                        name: 'created',
                    },
                    {
                        data: 'instructor_amount',
                        name: 'instructor_amount',
                    },
                    {
                        data: 'admin_commission_amount',
                        name: 'admin_commission_amount',
                    },
                    {
                        data: 'cprice',
                        name: 'cprice',
                    }
                    // {
                    //     data: 'total_amount',
                    //     name: 'total_amount',
                    // },
                    // {
                    //     data: 'tot_class_sub',
                    //     name: 'tot_class_sub',
                    // }

                ],
                order: ['0', 'DESC']
            });
        }

        var flag = true;
        // $('#filter').click(function() {
        //     var date = $('#date_filter').val();
        //     dateInterval = date.split(' - ');
        //     from_date = dateInterval[0];
        //     to_date = dateInterval[1];
        //     if (from_date != '' && to_date != '') {
        //         if (to_date < from_date) {
        //             alert('Start date must be less than end date');
        //             flag = false;
        //         } else {
        //             flag = true;

        //         }
        //     } else {
        //         alert('please select start date and end date');
        //         flag = false;
        //     }
        //     if (flag) {
        //         $('#report_admin').DataTable().destroy();
        //         $(document).find('#export_start_date').val(from_date);
        //         $(document).find('#export_end_date').val(to_date);
        //         $(document).find('#export_start_date1').val(from_date);
        //         $(document).find('#export_end_date1').val(to_date);
        //         load_data(from_date, to_date);

        //     }

        // });

        $('#filter').click(function() {
            var date = $('#date_filter').val();
            dateInterval = date.split(' - ');
            from_date = dateInterval[0];
            to_date = dateInterval[1];
            if (from_date != '' && to_date != '') {
                if (to_date < from_date) {
                    $('#span').show();
                    flag = false;
                } else {
                    $('#span').hide();
                    flag = true;

                }
            } else {
                $('#span').show();
                flag = false;
            }
            if (flag) {
                $('#report_admin').DataTable().destroy();
                $(document).find('#export_start_date').val(from_date);
                $(document).find('#export_end_date').val(to_date);
                $(document).find('#export_start_date1').val(from_date);
                $(document).find('#export_end_date1').val(to_date);
                load_data(from_date, to_date);

            }

        });
    });

    $(".export-form").click(function() {
        $('#export').submit();
    });
    $(".export-form1").click(function() {
        $('#export1').submit();
    });

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