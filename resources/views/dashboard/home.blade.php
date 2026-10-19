@extends('dashboard.layouts.master')
@section('title', 'Dashboard')
@push('after-styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/flags.css') }}" type="text/css" />
@endpush
@section('content')
    <style>
        .container {
            width: 100%;
            margin: 15px auto;
        }
        .icon-img img{
            height: 50px;
        }
    </style>
    <?php
    //use App\Models\MainUser;
    //use App\Models\SchoolProfile;
    //use App\Models\ManageProgram;
    ?>
    <div class="padding p-b-0 upskild-dashboard">
        <div class="margin">
            <div class="row filter_message">
                <div class="col-xs-6">
                    <h5 class="m-b-0 _300">{{ __('backend.hi') }} <span
                            class="text-primary">{{ Auth::user()->name }}</span>, {{ __('backend.welcomeBack') }}
                    </h5>
                </div>
                <div class="col-xs-6">
                    <form action="{{ route('dashboardfilter') }}" method="post" style="padding-left: %;" id="datafilter">
                        @csrf
                        <!-- <select name="action" id="action" class="form-control c-select w-sm inline v-middle">
                            <option value="no">Select</option>
                            <option value="1">Today</option>
                            <option value="2">Week</option>
                            <option value="3">Month</option>
                        </select> -->
                        <input type="text" class="form-control filter_message"
                            style="color: #001645;font-weight:500;width: 210px;margin-right: 8px;"
                            value="{{ isset($filterdate) ? $filterdate : old('date_filter') }}" placeholder="DD-MM-YYYY"
                            name="date_filter" id="date_filter" />
                        <input type="submit" name="filter_submit" class="btn btn-primary" value="Filter"
                            id="filter_submit" />
                        <a href="{{ route('adminHome') }}"><input type="button" name="clear" class="btn btn-danger"
                                value="Clear" /></a>

                        <input type="hidden" name="startdate" value="{{ isset($startdate) ? $startdate : '' }}"
                            id="export_start_date" class="start_date">
                        <input type="hidden" name="enddate" value="{{ isset($enddate) ? $enddate : '' }}"
                            id="export_end_date" class="end_date">
                    </form>
                </div>
                <span class="removeError1" style="color: red; display: none; margin-right: -25px;" id="span">Please
                    select start date and end date
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="row">
                    @php
                    $customer_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,2,'read');                                                 
                    @endphp
                    @if(isset($customer_module_permission) && $customer_module_permission==true)
                    <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="{{ route('customer') }}">
                                <div class="pull-left m-r" style="text-align: center !important;">
                                <img class = "icon-img" src="{{ asset('assets/dashboard/images/dashboard_icons/Countryadmin.svg')}}" style="height:50px;">
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Total Number of Customers</div>
                                    <h4 class="m-a-0 text-md _600">{{ isset($total_customer) ? $total_customer : 0 }}
                                    </h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endif
                    @php
                        $product_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,8,'read');                                                 
                    @endphp
                    @if(isset($product_module_permission) && $product_module_permission==true)
                    <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="{{ route('product') }}">
                                <div class="pull-left m-r icon-img">
                                    <img class ="icon-img" src="{{ asset('assets/dashboard/images/dashboard_icons/product.svg')}}" style="height:50px;">
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Total Number of Products</div>
                                    <h4 class="m-a-0 text-md _600">{{ isset($total_product) ? $total_product : 0 }}
                                    </h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endif
                    @php
                    $order_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,9,'read');                                                 
                    @endphp
                    @if(isset($order_module_permission) && $order_module_permission==true)
                    <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="#">
                                <div class="pull-left m-r" >
                                <img class="icon-img" src="{{ asset('assets/dashboard/images/dashboard_icons/Order management.svg')}}" style="height:50px;">
                                </div>
                                <div class="clear">
                                    <div class="text-muted">Total number of in process orders </div>
                                    <h4 class="m-a-0 text-md _600">{{ isset($order_inprogress) ? $order_inprogress : 0 }}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12">
                @php
                $order_module_permission = @Helper::GetRolePermission(Auth::user()->user_type,9,'read');                                                 
                @endphp
                @if(isset($order_module_permission) && $order_module_permission==true)
                <div class="row">
                    <div class="col-xs-4">
                        <div class="box p-a" style="cursor: pointer">
                            <a href="#">
                                <div class="pull-left m-r">
                                <img class="icon-img" src="{{ asset('assets/dashboard/images/dashboard_icons/Order management.svg')}}" style="height:50px;">

                                </div>
                                <div class="clear">
                                    <div class="text-muted">Total number of delivered orders</div>
                                    <h4 class="m-a-0 text-md _600"> {{ isset($total_deliver_order) ? $total_deliver_order : 0 }}</h4>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
@endsection
@push('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(function() {
            let dateInterval = getQueryParameter('date_filter');
            let start = "<?php echo $start; ?> ";
            let end = "<?php echo $end; ?> ";

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
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                maxDate: new Date(),
                startDate: start,
                endDate: end,
                locale: {
                   /// format: 'DD/MM/YYYY',
                    firstDay: 1,
                },
            });
            $('#date_filter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format(
                    'DD-MM-YYYY'));
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
