@extends('wholesaler.layouts.master')
@section('title','Dashboard')
@push("after-styles")
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/flags.css') }}" type="text/css" />
@endpush
@section('content')
<style>
    .container {
        width: 100%;
        margin: 15px auto;
    }
</style>
<?php
//use App\Models\MainUser;
//use App\Models\SchoolProfile;
//use App\Models\ManageProgram;
// $test = auth()->guard('main_user')->user();
// echo "<pre>";print_r($test);exit();
?>
<div class="padding p-b-0 upskild-dashboard">
    <div class="margin">
        <div class="row filter_message">
            <div class="col-xs-6">
                <h5 class="m-b-0 _300">{{ __('backend.hi') }} <span class="text-primary">{{ isset(auth()->guard('main_user')->user()->name) ? auth()->guard('main_user')->user()->name : '' }}</span>, {{ __('backend.welcomeBack') }}
                </h5>
            </div>
            <div class="col-xs-6">
                <!-- <form action="{{ route('dashboardfilter')}}" method="post" style="padding-left: %;" id="datafilter">
                    @csrf
                   
                    <input type="text" class="form-control filter_message" style="color: #001645;font-weight:500;width: 200px;margin-right: 8px;" value="{{ isset($filterdate)?$filterdate:old('date_filter') }}" placeholder="MM-DD-YYYY" name="date_filter" id="date_filter" />
                    <input type="submit" name="filter_submit" class="btn btn-primary" value="Filter" id="filter_submit" />
                    <a href="{{ route('adminHome')}}"><input type="button" name="clear" class="btn btn-danger" value="Clear" /></a>

                     <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : ''}}" id="export_start_date" class="start_date">
                    <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : ''}}" id="export_end_date" class="end_date">
                </form> -->
            </div>
            <span class="removeError1" style="color: red; display: none; margin-right: -25px;" id="span">Please select start date and end date
            </span>
        </div>
    </div>


</div>
@endsection
@push("after-scripts")
 <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(function () {
            let dateInterval = getQueryParameter('date_filter');
           let start = "<?php echo ($start);?> ";
           let end = "<?php echo ($end);?> ";
            
            if (dateInterval) {
                dateInterval = dateInterval.split(' - ');
                start = dateInterval[0];
                end = dateInterval[1];
            }
            $('#date_filter').daterangepicker({
                "showDropdowns": true,
                "showWeekNumbers": true,
                "alwaysShowCalendars": true,
                maxDate: new Date(),
                startDate: start,
                endDate: end,
                locale: {
                    format: 'MM/DD/YYYY',
                    firstDay: 1,
                },
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