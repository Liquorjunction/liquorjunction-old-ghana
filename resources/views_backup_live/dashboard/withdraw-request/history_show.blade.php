@extends('dashboard.layouts.master')
@section('title', 'Show Withdraw History')
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
            <h3>Show Withdraw History</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / Show Withdraw History
                <!-- <a href="{{ route('user-withdraw-history') }}">Show Withdraw History</a> -->
            </small>
        </div>
        {{-- <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('user-withdraw-history')}}">
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
                                        <th>Email</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->email}}</td>
                                        @endif        
                                        @endforeach
                                    </tbody>
                                </tr>
                                <!-- <tr>
                                    <tbody>
                                        <th>Country Code</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{"+".$user->country_code}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr> -->
                                <tr>
                                    <tbody>
                                        <th>Mobile Number</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{"+".$user->country_code.'-'.$user->phone}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>About Me</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->about_me}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Dance Instructor (He/She)</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        @if($user->category_dance_instructor == 1)
                                        <td>Male</td>
                                        @else
                                        <td>Female</td>
                                        @endif
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Image</th>
                                        <td>
                                            @foreach($Users as $key => $user)
                                            @if($user->id == $withdraw_history->instructor_id)
                                            @if(isset($user->profile) && $user->profile != "")
                                            <img id="image" src="{{ asset('uploads/website_users/').'/'.$user->profile }}" class="thumbnail" width="100px" height="100px" />
                                            @else
                                            <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                            @endif
                                            @endif        
                                            @endforeach
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Dance Group Name</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->dance_group_name}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Request Status</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        @if($user->is_verify_instructor == 1)
                                            <td>Pending</td>
                                        @elseif($user->is_verify_instructor == 2)
                                            <td>Approved</td>
                                        @else
                                            <td>Rejected</td>
                                        @endif  
                                        @endif        
                                        @endforeach          
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Popular Instructor</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        @if($user->is_popular_insructor == 1)
                                            <td>Yes</td>
                                        @else
                                            <td>No</td>
                                        @endif  
                                        @endif        
                                        @endforeach           
                                    <tbody>
                                </tr>
                                {{-- <tr>
                                    <tbody>
                                        <th>Current Plan</th>
                                        <td>{{isset($Users->current_plan_id) ? $Users->current_plan_id : 'No'}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Plan Expiry Date</th>
                                        <?php $date = \Helper::formatDatetime($Users->plan_expiry_date) . ' ' . date('H:i:s', strtotime($Users->plan_expiry_date)) ?>
                                        <td>{{ $date }}</td>
                                    <tbody>
                                </tr> --}}
                                <tr>
                                    <tbody>
                                        <th>Instructor Since</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <?php 
                                            // $date = \Helper::formatDatetime($user->instructor_since) . ' ' . date('H:i:s', strtotime($user->instructor_since)) 

                                            $createddate = \Helper::converttimeTozone($user->instructor_since);
                                            ?>
                                        <td>{{ $createddate }}</td>
                                        @endif        
                                        @endforeach    
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor facebook Link</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->instructor_facebook_link}}</td>
                                        @endif        
                                        @endforeach 
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor Instagram Link</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->instructor_instagram_link}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor Tiktok Link</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->instructor_tiktok_link}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor Web Link</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->instructor_web_link}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor Location</th>
                                        @foreach($Users as $key => $user)
                                        @if($user->id == $withdraw_history->instructor_id)
                                        <td>{{$user->instructor_location}}</td>
                                        @endif        
                                        @endforeach
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Portfolio Image</th>
                                        <td>
                                            @foreach($Users as $key => $user)
                                            @if($user->id == $withdraw_history->instructor_id)
                                            @if(isset($user->instructor_portfolio_image) && $user->instructor_portfolio_image != "")
                                            <img id="image" src="{{ asset('uploads/website_users/').'/'.$user->instructor_portfolio_image }}" class="thumbnail" width="100px" height="100px" />
                                            @else
                                            <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                            @endif
                                            @endif        
                                            @endforeach
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Portfolio Video</th>
                                        <td>
                                            @foreach($Users as $key => $user)
                                            @if($user->id == $withdraw_history->instructor_id)
                                            @if(isset($user->instructor_portfolio_video) && $user->instructor_portfolio_video != "")
                                            <video width="200" controls  class="video-link" id="video-link">
                                                <source src="{{ asset('uploads/website_users/videos/').'/'.$user->instructor_portfolio_video }}" class="video_here">
                                                  Your browser does not support HTML5 video.
                                              </video>
                                            @else
                                            <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                            @endif
                                            @endif        
                                            @endforeach
                                        </td>
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