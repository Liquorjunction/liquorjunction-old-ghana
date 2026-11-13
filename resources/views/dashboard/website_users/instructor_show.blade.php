@extends('dashboard.layouts.master')
@section('title', 'Show User')
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
            <h3><?php echo $Users->name; ?></h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / Show User
                <!-- <a href="{{ route('users') }}">Show User</a> -->
            </small>
        </div>
        <div class="box-tool">
            <ul class="nav">
                <li class="nav-item inline">
                    <a class="nav-link" href="{{route('users')}}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div>



        <div class="box nav-active-border b-info">

            <div class="tab-content clear b-t">
                <div class="tab-pane active" id="tab_details">
                    <div class="box-body">


                        <form class="table_design">


                            <table class="table table-bordered m-a-0">

                                <tr>
                                    <tbody>
                                        <th>User Type</th>
                                        @if(isset($Users->user_type) && $Users->user_type == 2)
                                            <td>Normal</td>
                                        @else
                                            <td>Instructor</td>
                                        @endif        
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Name</th>
                                        <td>{{$Users->name}}</td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Email</th>
                                        <td>{{$Users->email}}</td>
                                    </tbody>
                                </tr>
                                <!-- <tr>
                                    <tbody>
                                        <th>Country Code</th>
                                        <td>{{"+".$Users->country_code}}</td>
                                    <tbody>
                                </tr> -->
                                <tr>
                                    <tbody>
                                        <th>Mobile Number</th>
                                        <td>{{"+".$Users->country_code.' '.$Users->phone}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>About Me</th>
                                        <td>{{$Users->about_me}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Dance Instructor (He/She)</th>
                                        @if($Users->category_dance_instructor == 1)
                                        <td>Male</td>
                                        @else
                                        <td>Female</td>
                                        @endif
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Image</th>
                                        <td>

                                            @if(isset($Users->profile) && $Users->profile != "")
                                            <img id="image" src="{{ asset('uploads/website_users/').'/'.$Users->profile }}" class="thumbnail" width="100px" height="100px" />
                                            @else
                                            <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                            @endif
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Dance Group Name</th>
                                        <td>{{$Users->dance_group_name}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Request Status</th>
                                        @if($Users->is_verify_instructor == 1)
                                            <td>Pending</td>
                                        @elseif($Users->is_verify_instructor == 2)
                                            <td>Approved</td>
                                        @else
                                            <td>Rejected</td>
                                        @endif            
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Popular Instructor</th>
                                        @if($Users->is_popular_insructor == 1)
                                            <td>Yes</td>
                                        @else
                                            <td>No</td>
                                        @endif            
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
                                        <?php 

                                        // $date = \Helper::formatDatetime($Users->instructor_since) . ' ' . date('H:i:s', strtotime($Users->instructor_since)) 

                                        $createddate = \Helper::converttimeTozone($Users->instructor_since);

                                        ?>
                                        <td>{{ $createddate }}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor facebook Link</th>
                                        <td>{{$Users->instructor_facebook_link}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor Instagram Link</th>
                                        <td>{{$Users->instructor_instagram_link}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor Tiktok Link</th>
                                        <td>{{$Users->instructor_tiktok_link}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor Web Link</th>
                                        <td>{{$Users->instructor_web_link}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Instructor Location</th>
                                        <td>{{$Users->instructor_location}}</td>
                                    <tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Portfolio Image</th>
                                        <td>

                                            @if(isset($Users->instructor_portfolio_image) && $Users->instructor_portfolio_image != "")
                                            <img id="image" src="{{ asset('uploads/website_users/').'/'.$Users->instructor_portfolio_image }}" class="thumbnail" width="100px" height="100px" />
                                            @else
                                            <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                            @endif
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Portfolio Video</th>
                                        <td>
                                            @if(isset($Users->instructor_portfolio_video) && $Users->instructor_portfolio_video != "")
                                            <video width="200" controls  class="video-link" id="video-link">
                                                <source src="{{ asset('uploads/website_users/videos/').'/'.$Users->instructor_portfolio_video }}" class="video_here">
                                                  Your browser does not support HTML5 video.
                                              </video>
                                            @else
                                            <img src="{{ asset('uploads/contacts/noimage.png') }}" width="100px" height="100px">
                                            @endif
                                        </td>
                                    </tbody>
                                </tr>
                                <tr>
                                    <tbody>
                                        <th>Created Date/Time</th>
                                        <?php 

                                        // $date = \Helper::formatDatetime($Users->created_at) . ' ' . date('H:i:s', strtotime($Users->created_at)) 

                                        $createddate = \Helper::converttimeTozone($Users->created_at);
                                        ?>
                                        <td>{{ $createddate }}</td>
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