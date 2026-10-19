@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>My Account</h1>
                            </div>
                        </div>
                    </div>
                </div>
            <!--Page Title-->
            <!--Breadcrumb-->
                <div class="breadcrumb_cover">
                    <div class="container">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od" /></a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Account</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!--Breadcrumb-->

            <!--My Account Page-->
                <section class="my_account">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="account_sidebar">
                                    <ul>
                                        @if(isset(auth()->guard('main_user')->user()->id) && auth()->guard('main_user')->user()->id > 0)
                                            @if(auth()->guard('main_user')->user()->user_type == 3)
                                            <li class="{{ request()->is('profile') ? 'active' : '' }}"><a href="{{route('websiteprofile')}}">Profile </a></li>
                                            <li class="{{ request()->is('my-library') ? 'active' : '' }}"><a href="{{route('mylibrary')}}">My Library </a></li>
                                            <li class="{{ request()->is('my-class') ? 'active' : '' }}"><a href="{{route('my-class')}}">My Classes</a></li>
                                            <li class="{{ request()->is('my-earning') ? 'active' : '' }}"><a href="{{route('myearning')}}">My Earning/History</a></li>
                                            <li class="{{ request()->is('my-favourite') ? 'active' : '' }}"><a href="{{route('my-favourite')}}">My Favourite</a></li>
                                            <li class="{{ request()->is('change-password') ? 'active' : '' }}"><a href="{{route('user-change-password')}}">Change Password</a></li>
                                            <li><a id="logout" href="{{ url('/logout') }}">Logout</a><form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                            </form></li>
                                            @else
                                            <li class="{{ request()->is('profile') ? 'active' : '' }}"><a href="{{route('websiteprofile')}}">Profile </a></li>
                                            <li class="{{ request()->is('my-library') ? 'active' : '' }}"><a href="{{route('mylibrary')}}">My Library </a></li>
                                            <li class="{{ request()->is('my-favourite') ? 'active' : '' }}"><a href="{{route('my-favourite')}}">My Favourite</a></li>
                                            <li class="{{ request()->is('change-password') ? 'active' : '' }}"><a href="{{route('user-change-password')}}">Change Password</a></li>
                                            <li><a id="logout" href="{{ url('/logout') }}">Logout</a><form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                            </form></li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @if(isset($users))
                            @if(auth()->guard('main_user')->user()->user_type == 2)
                            <div class="col-lg-9">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="instructor_left">
                                            <div class="instructor_img">
                                                @if(auth()->guard('main_user')->user()->profile != null)
                                                <img src="{{ asset('uploads/website_users/').'/'.$users->profile }}" alt="instructors_img" />
                                                @else
                                                <img src="{{ asset('assets/frontend/images/instructors_img3.jpg') }}" alt="instructors_img" />
                                                @endif
                                            </div>
                                            <div class="insturctor_name">
                                                <h2>{{$users->name}}</h2>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile_cover">
                                            <div class="edit_profile">
                                                <a href="{{route('websiteuserprofile')}}" class="common_btn">Edit Profile</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="about_me">
                                            <h3>About Me</h3>
                                            <p>{{$users->about_me}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="col-lg-9">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="instructor_left">
                                            <div class="instructor_img">
                                                @if(auth()->guard('main_user')->user()->profile != null)
                                                <img src="{{ asset('uploads/website_users/').'/'.$users->profile }}" alt="instructors_img" />
                                                @else
                                                <img src="{{ asset('assets/frontend/images/instructors_img3.jpg') }}" alt="instructors_img" />
                                                @endif
                                            </div>
                                            <div class="insturctor_name">
                                                <h2>{{$users->name}}</h2>
                                                <ul class="course_type">
                                                    <li class="course_box orange_box"><span>{{$users->category_name}}</span></li>
                                                    <li class="course_box yellow_box"><span>{{$users->dance_level_title}}</span></li>
                                                    <li class="course_box blue_box"><span>{{$users->duration}}</span></li>
                                                </ul>
                                                <div class="instructor_social">
                                                    <ul>
                                                        <li><a href="{{$users->instructor_facebook_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/fb_icon_grey.svg') }}" alt="fb_icon_grey"></a></li>
                                                        <li><a href="{{$users->instructor_instagram_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/insta_icon_grey.svg') }}" alt="insta_icon_grey"></a></li>
                                                        <li><a href="{{$users->instructor_web_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/twitter_icon_grey.svg') }}" alt="twitter_icon_grey"></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile_cover">
                                            <?php
                                                    $parameter =[
                                                        'id' =>auth()->guard('main_user')->user()->id,
                                                    ];
                                               // $parameter= Crypt::encrypt($parameter);
                                                $parameter = base64_encode(auth()->guard('main_user')->user()->id);
                                            ?>
                                            <div class="edit_profile">
                                                @if(auth()->guard('main_user')->user()->user_type == 3)
                                                <a href="{{route('websiteinstructorprofile')}}" class="common_btn">Edit Profile</a>
                                                @else
                                                <a href="javascript:void()" class="common_btn">Edit Profile</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="about_me">
                                            <h3>About Me</h3>
                                            <p>{{$users->about_me}}</p>
                                            <?php $since_date = $users->created_at; 
                                                  $sdate = explode('-',$since_date);
                                            ?>
                                            <span>Since {{$sdate[0]}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="contact_instructor">
                                            <ul>
                                                <li class="instru_email"><a href="mailto:{{$users->email}}">{{$users->email}}</a></li>
                                                <li class="instru_phone"><a href="tel:{{$users->phone}}">{{$users->phone}}</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="post_title">
                                            <h2>My Post</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="profile_tabs">
                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="posts" aria-selected="true">Images</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes" type="button" role="tab" aria-controls="classes" aria-selected="false">Videos</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="myTabContent">
                                                <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="posts-tab">
                                                    <div class="row">
                                                        @if(isset($post) && !empty($post))
                                                        @foreach($post as $key => $p)
                                                        @if($p->file != null)
                                                        <div class="col-md-4 col-sm-6">
                                                            <div class="tab_image">
                                                                <img src="{{ asset('uploads/website_users/post/images').'/'.$p->file }}" alt="dance_img" />
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                        <!-- <div class="col-md-4 col-sm-6 tab_video_col">
                                                            <div class="tab_image">
                                                                <img src="{{ asset('assets/frontend/images/popular_classes_img2.jpg') }}" alt="dance_img" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-sm-6">
                                                            <div class="tab_image">
                                                                <img src="{{ asset('assets/frontend/images/popular_classes_img3.jpg') }}" alt="dance_img" />
                                                            </div>
                                                        </div> -->
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="classes" role="tabpanel" aria-labelledby="classes-tab" id="tabList">
                                                    <div class="row">
                                                        @if(isset($post) && !empty($post))
                                                        @foreach($post as $key => $p)
                                                        @if($p->video_file != null)
                                                        <div class="col-md-4 col-sm-6">
                                                            <div class="tab_video_cover">
                                                                <video muted controls="controls" add controlsList="nodownload">
                                                                <source src="{{ asset('uploads/website_users/post/videos').'/'.$p->video_file }}" type="video/mp4">
                                                                </video>
                                                                <!-- <img src="{{ asset('uploads/website_users/post/images').'/'.$p->file }}" alt="dance_img" /> -->
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                        <!-- <div class="col-md-4 col-sm-6">
                                                            <div class="course_cover_listing">
                                                                <div class="list_box">
                                                                    <div class="list_box_video">
                                                                        <a href="#">
                                                                            <img class="listing_popular_img" src="{{ asset('assets/frontend/images/listing_popular_img2.jpg') }}" alt="listing_popular_img">
                                                                            <span class="play_icon">
                                                                                <img src="{{ asset('assets/frontend/images/play_icon_small.png') }}" alt="play_icon">
                                                                            </span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="course_details">
                                                                    <ul class="course_type">
                                                                        <li class="course_box orange_box"><span>Krump</span></li>
                                                                        <li class="course_box green_box"><span>Basic</span></li>
                                                                        <li class="course_box blue_box"><span>30min</span></li>
                                                                    </ul>
                                                                    <h3><a href="#">Cource title goes here longer name.</a></h3>
                                                                    <ul class="price_nav">
                                                                        <li class="main_price">
                                                                            <span>&#8381;</span>
                                                                            <h4>500</h4>
                                                                        </li>
                                                                        <li class="offer_price">
                                                                            <h4 class="grey_color">&#8381; 1100</h4>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="course_author course_author_cover">
                                                                        <div class="course_author_left">
                                                                            <img src="{{ asset('assets/frontend/images/author2.png') }}" alt="author_img">
                                                                            <span>Jose Portilla</span>
                                                                        </div>
                                                                        <div class="fav_list">
                                                                            <a href="javascript:(void)">
                                                                                <img src="{{ asset('assets/frontend/images/heart_icon.svg') }}" alt="heart_icon">
                                                                                <span>Favourite</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <div class="col-md-4 col-sm-6">
                                                            <div class="course_cover_listing">
                                                                <div class="list_box">
                                                                    <div class="list_box_video">
                                                                        <a href="#">
                                                                            <img class="listing_popular_img" src="{{ asset('assets/frontend/images/listing_popular_img3.jpg') }}" alt="listing_popular_img">
                                                                            <span class="play_icon">
                                                                                <img src="{{ asset('assets/frontend/images/play_icon_small.png') }}" alt="play_icon">
                                                                            </span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="course_details">
                                                                    <ul class="course_type">
                                                                        <li class="course_box orange_box"><span>Dancehall</span></li>
                                                                        <li class="course_box pink_box"><span>Advance</span></li>
                                                                        <li class="course_box blue_box"><span>20min</span></li>
                                                                    </ul>
                                                                    <h3><a href="#">Cource title goes here longer name.</a></h3>
                                                                    <ul class="price_nav">
                                                                        <li class="main_price">
                                                                            <span>&#8381;</span>
                                                                            <h4>400</h4>
                                                                        </li>
                                                                        <li class="offer_price">
                                                                            <h4 class="grey_color">&#8381; 1000</h4>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="course_author course_author_cover">
                                                                        <div class="course_author_left">
                                                                            <img src="{{ asset('assets/frontend/images/author1.png') }}" alt="author_img">
                                                                            <span>Kyle Pew</span>
                                                                        </div>
                                                                        <div class="fav_list">
                                                                            <a href="javascript:(void)">
                                                                                <img src="{{ asset('assets/frontend/images/heart_icon.svg') }}" alt="heart_icon">
                                                                                <span>Favourite</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="add_img_vid">
                                            <h2>Add Images & Video</h2>
                                            <div class="upload__box ">
                                                <div class="upload__btn-box">
                                                    <div class="upload__img-wrap"></div>
                                                    <label class="upload__btn">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M20.75 10.75H13.25V3.25C13.25 2.91848 13.1183 2.60054 12.8839 2.36612C12.6495 2.1317 12.3315 2 12 2C11.6685 2 11.3505 2.1317 11.1161 2.36612C10.8817 2.60054 10.75 2.91848 10.75 3.25V10.75H3.25C2.91848 10.75 2.60054 10.8817 2.36612 11.1161C2.1317 11.3505 2 11.6685 2 12C2 12.3315 2.1317 12.6495 2.36612 12.8839C2.60054 13.1183 2.91848 13.25 3.25 13.25H10.75V20.75C10.75 21.0815 10.8817 21.3995 11.1161 21.6339C11.3505 21.8683 11.6685 22 12 22C12.3315 22 12.6495 21.8683 12.8839 21.6339C13.1183 21.3995 13.25 21.0815 13.25 20.75V13.25H20.75C21.0815 13.25 21.3995 13.1183 21.6339 12.8839C21.8683 12.6495 22 12.3315 22 12C22 11.6685 21.8683 11.3505 21.6339 11.1161C21.3995 10.8817 21.0815 10.75 20.75 10.75Z" fill="#ff8200"></path>
                                                        </svg>
                                                        
                                                        <input type="file" data-max_length="1" class="upload__inputfile" id="file" accept="image/*, video/mp4" name="file">
                                                    </label>
                                                    <div class="text-center" id="loader" style="display:none;">
                                                        <img class="logo-img" alt="" src="{{ asset('assets/dashboard/images/loading.gif')}}" style="margin: 20px auto; margin-left: 100px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @endif
                            @if(isset($users1))
                            <div class="col-lg-9">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="instructor_left">
                                            <div class="instructor_img">
                                                @if(auth()->guard('main_user')->user()->profile != null)
                                                <img src="{{ asset('uploads/website_users/').'/'.$users1->profile }}" alt="instructors_img" />
                                                @else
                                                <img src="{{ asset('assets/frontend/images/instructors_img3.jpg') }}" alt="instructors_img" />
                                                @endif
                                            </div>
                                            <div class="insturctor_name">
                                                <h2>{{$users1->name}}</h2>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile_cover">
                                            <div class="edit_profile">
                                                <a href="javascript:void()" class="common_btn">Edit Profile</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="about_me">
                                            <h3>About Me</h3>
                                            <p>{{$users1->about_me}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </section>
                <!--My Account Page-->
        </div>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="crossorigin="anonymous"></script>
        <script type="text/javascript">
            $(document).on("click", "#logout", function(e) {
                        // alert('hello')
                            e.preventDefault();
                            var link = $(this).attr("href");
                            // alert(link)
                            // return false;
                            Swal.fire({
                              title: 'Logout ?',
                              text: "Are you sure you want to logout ?",
                              icon: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Yes'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                $('#logout-form').submit();
                              }
                            })
                });
            //posts-tab
            //classes-tab
            // $('#posts-tab').on('click', function(){
            //     var ref_this = $('ul.tabs li a.active');
            //    // alert("first tab");

            //     $('input[type="file"]').change(function() {
            //         var fd = new FormData();
            //         var files = $('#file')[0].files;

            //         loadershow();
                    
            //         // Check file selected or not
            //         if(files.length > 0 ){
            //            fd.append('file',files[0]);

            //            $.ajax({
            //               url: '{{route('imagefileupload')}}',
            //               type: 'post',
            //               data: fd,
            //               contentType: false,
            //               processData: false,
            //               success: function(response){
            //                  loaderhide();
            //                  window.location.reload();
            //               },
            //            });
            //         }else{
                       
            //         }
            //     });
            // });

            // $('#classes-tab').on('click', function(){
            //     var ref_this = $('ul.tabs li a.active');
            //    // alert("second tab");

            //     $('input[type="file"]').change(function() {
            //         var fd = new FormData();
            //         var files = $('#file')[0].files;

            //         loadershow();
                    
            //         // Check file selected or not
            //         if(files.length > 0 ){
            //            fd.append('file',files[0]);

            //            $.ajax({
            //               url: '{{route('videofileupload')}}',
            //               type: 'post',
            //               data: fd,
            //               contentType: false,
            //               processData: false,
            //               success: function(response){
            //                 loaderhide();
            //                 window.location.reload();
            //               },
            //            });
            //         }else{
                       
            //         }
            //     });
            // });

            // if($("button:contains('Images')"))
            // {
            //     $('input[type="file"]').change(function() {
            //         var fd = new FormData();
            //         var files = $('#file')[0].files;

            //         loadershow();
                    
            //         // Check file selected or not
            //         if(files.length > 0 ){
            //            fd.append('file',files[0]);

            //            $.ajax({
            //               url: '{{route('imagefileupload')}}',
            //               type: 'post',
            //               data: fd,
            //               contentType: false,
            //               processData: false,
            //               success: function(response){
            //                 loaderhide();
            //                  window.location.reload();
            //               },
            //            });
            //         }else{
                       
            //         }
            //     });
            // }

            // if($("button:contains('Videos')"))
            // {
            //     var ref_this = $('ul.tabs li a.active');
            //        // alert("second tab");

            //         $('input[type="file"]').change(function() {
            //             var fd = new FormData();
            //             var files = $('#file')[0].files;

            //             loadershow();
                        
            //             // Check file selected or not
            //             if(files.length > 0 ){
            //                fd.append('file',files[0]);

            //                $.ajax({
            //                   url: '{{route('videofileupload')}}',
            //                   type: 'post',
            //                   data: fd,
            //                   contentType: false,
            //                   processData: false,
            //                   success: function(response){
            //                     loaderhide();
            //                     window.location.reload();
            //                   },
            //                });
            //             }else{
                           
            //             }
            //     });
            // }

            // $("#but_upload").click(function(){

            //     if($("button:contains('Images')"))
            //     {
            //         var fd = new FormData();
            //         var files = $('#file')[0].files;

            //         loadershow();
                    
            //         // Check file selected or not
            //         if(files.length > 0 ){
            //            fd.append('file',files[0]);

            //            $.ajax({
            //               url: '{{route('imagefileupload')}}',
            //               type: 'post',
            //               data: fd,
            //               contentType: false,
            //               processData: false,
            //               success: function(response){
            //                 loaderhide();
            //                 window.location.reload();
            //               },
            //            });
            //         }else{
                       
            //         }
            //     }
            //     else
            //     {
            //         var ref_this = $('ul.tabs li a.active');
            //        // alert("second tab");

            //         $('input[type="file"]').change(function() {
            //             var fd = new FormData();
            //             var files = $('#file')[0].files;

            //             loadershow();
                        
            //             // Check file selected or not
            //             if(files.length > 0 ){
            //                fd.append('file',files[0]);

            //                $.ajax({
            //                   url: '{{route('videofileupload')}}',
            //                   type: 'post',
            //                   data: fd,
            //                   contentType: false,
            //                   processData: false,
            //                   success: function(response){
            //                     loaderhide();
            //                     window.location.reload();
            //                   },
            //                });
            //             }else{
                           
            //             }
            //         });
            //     }
            // });

            $("input:file").change(function ()
            {
                const file = this.files[0];
                const fileType = file["type"];
               // alert(fileType);

                const validImageTypes = ["image/jpg", "image/jpeg", "image/png"];
                const validImageTypes1 = ["video/mp4"];
                if ($.inArray(fileType, validImageTypes) > 0) {
                       
                    var fd = new FormData();
                    var files = $('#file')[0].files;

                    loadershow();
                    
                    // Check file selected or not
                    if(files.length > 0 ){
                       fd.append('file',files[0]);

                       $.ajax({
                          url: '{{route('imagefileupload')}}',
                          type: 'post',
                          data: fd,
                          contentType: false,
                          processData: false,
                          success: function(response){
                            loaderhide();
                            window.location.reload();
                          },
                       });
                    }else{
                       
                    }
                }
                else if($.inArray(fileType, validImageTypes) < 0)
                {
                    var fd = new FormData();
                    var files = $('#file')[0].files;

                    loadershow();
                    
                    // Check file selected or not
                    if(files.length > 0 ){
                       fd.append('file',files[0]);

                       $.ajax({
                          url: '{{route('videofileupload')}}',
                          type: 'post',
                          data: fd,
                          contentType: false,
                          processData: false,
                          success: function(response){
                            loaderhide();
                            window.location.reload();
                          },
                       });
                    }else{
                       
                    }
                }
                else
                {
                    console.log("file not found");
                }

            });

          function loadershow(){
            $("#center-block").attr('disabled','disabled');
            $('#loader').show();                               
          } 

          function loaderhide(){
             
             $('#loader').hide();                               
           } 
        </script>
@endsection
