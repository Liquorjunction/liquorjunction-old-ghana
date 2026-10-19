@extends('dashboard.layouts.master')
@section('title', 'Show Withdraw Request')
@section('content')

<link href="{{ asset('assets/dashboard/css/select2.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" type="text/css" />
<style type="text/css">
    .select2-container {
        width: 100% !important;
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #ffffff;
        opacity: 1;
    }

    .table tbody>tr>td,
    .table thead>tr>th {

        border-left: 1px solid #dfdfdf !important;

    }

    .table_design {
        padding: 0 !important;
    }

    .table_design th {
        width: 200px !important;
    }
</style>

<div class="padding list-school">
    <div class="box">
        <div class="box-header dker">
            <h3>Show Withdraw Request</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / Show Withdraw Request
                <!-- <a href="{{ route('user-withdraw-request') }}">Show Withdraw Request</a> -->
            </small>
        </div>
        {{-- <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('user-withdraw-request')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div> --}}



        <div class="box nav-active-border b-info">

            <div class="tab-content clear b-t">
                <div class="tab-pane active" id="tab_details">
                    <div class="box-body">


                        <form class="table_design">


                            <table class="table table-bordered m-a-0">

                                <tr>
                                    <tbody>
                                        <th>User Type</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        @if(isset($user->user_type) && $user->user_type == 2)
                                            <td>Normal</td>
                                        @else
                                            <td>Instructor</td>    
                                        @endif 
                                        @endif       
                                        @endforeach
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Name</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->name}}</td>
                                        @endif        
                                        @endforeach
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Requested Date/Time</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <?php 
                                            // $date = \Helper::formatDatetime($withdraw_history->created_at) . ' ' . date('H:i:s', strtotime($withdraw_history->created_at)) 

                                            $createddate = \Helper::converttimeTozone($withdraw_history->created_at);
                                            ?>
                                        <td>{{ $createddate }}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Requested Amount</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$setting->currency_symbol.' '.$withdraw_history->amount.'.00'}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Account Balance</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$setting->currency_symbol.' '.$withdraw_history->balance.'.00'}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Request Status</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        @if($withdraw_history->request_status == 0)
                                            <td>Requested</td>
                                        @elseif($withdraw_history->request_status == 1)
                                            <td>Paid</td>
                                        @else
                                            <td>Denied</td>
                                        @endif  
                                        @endif        
                                        @endforeach          
                                    <tbody>
                                </tr>

                            </table>




                        </form>



                        <div class="form-group row">
                            <div class="col-sm-2">
                                <a href="{{ url()->previous() }}" class="btn btn-default m-t" style="margin: 0 0 0 0px">
                                    <i class="material-icons">
                                        &#xe5cd;</i> Cancel
                                </a>
                            </div>
                            <div class="col-sm-10">

                            </div>
                        </div>

                        {{Form::close()}}
                    </div>
                </div>


            </div>

        </div>
    </div>
    @endsection
    @push("after-scripts")

    <script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
async defer></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $(".tab-content :input").prop("disabled", true);
    </script>
    @endpush