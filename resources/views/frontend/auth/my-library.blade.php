@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
<style type="text/css">
    .center {
        text-align: center;
        margin-top: 100px;
    }
    .no_content{
        font-weight: bold;
        font-size: xx-large;
    }
</style>
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>My Library</h1>
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
                            <li class="breadcrumb-item active" aria-current="page">My Library</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--Breadcrumb-->

            <!--My Library Page-->
            <section class="my_account">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="account_sidebar_cover">
                                <div class="account_sidebar">
                                    <ul>
                                        @if(isset(auth()->guard('main_user')->user()->id) && auth()->guard('main_user')->user()->id > 0)
                                            @if(auth()->guard('main_user')->user()->user_type == 3)
                                            <li class="{{ request()->is('profile') ? 'active' : '' }}"><a href="{{route('websiteprofile')}}">Profile </a></li>
                                            <li class="{{ request()->is('my-library') ? 'active' : '' }}"><a href="{{route('mylibrary')}}">My Library </a></li>
                                            <li class="{{ request()->is('my-class') ? 'active' : '' }}"><a href="{{route('my-class')}}">My Classes</a></li>
                                            <li class="{{ request()->is('my-earning') ? 'active' : '' }}"><a href="{{route('myearning')}}">My Earning/History</a></li>
                                            <li class="{{ request()->is('my-favourite') ? 'active' : '' }}"><a href="{{route('my-favourite')}}">My Favourite</a></li>
                                            @if(auth()->guard('main_user')->user()->google_id == null || auth()->guard('main_user')->user()->facebook_id == null)
                                            <li class="{{ request()->is('change-password') ? 'active' : '' }}"><a href="{{route('user-change-password')}}">Change Password</a></li>
                                            @endif
                                            <!-- <li><a id="logout" href="{{ url('/logout') }}">Logout</a><form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                            </form></li> -->
                                            @else
                                            <li class="{{ request()->is('profile') ? 'active' : '' }}"><a href="{{route('websiteprofile')}}">Profile </a></li>
                                            <li class="{{ request()->is('my-library') ? 'active' : '' }}"><a href="{{route('mylibrary')}}">My Library </a></li>
                                            <li class="{{ request()->is('my-favourite') ? 'active' : '' }}"><a href="{{route('my-favourite')}}">My Favourite</a></li>
                                            @if(auth()->guard('main_user')->user()->google_id == null || auth()->guard('main_user')->user()->facebook_id == null)
                                            <li class="{{ request()->is('change-password') ? 'active' : '' }}"><a href="{{route('user-change-password')}}">Change Password</a></li>
                                            @endif
                                            <!-- <li><a id="logout" href="{{ url('/logout') }}">Logout</a><form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                            </form></li> -->
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                                <div class="logout_form">
                                    <a id="logout" href="{{ url('/logout') }}">Logout</a>
                                    <form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                        </div>
                                                        
                        <div class="col-lg-9">
                            <div class="listing_right">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="library_tab">
                                            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="pills-purchased-tab" data-bs-toggle="pill" data-bs-target="#pills-purchased" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Purchased</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-favourite-tab" data-bs-toggle="pill" data-bs-target="#pills-favourite" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Favourite</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-completed-tab" data-bs-toggle="pill" data-bs-target="#pills-completed" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Completed</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="pills-inprogress-tab" data-bs-toggle="pill" data-bs-target="#pills-inprogress" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">In progress</button>
                                                </li>
                                            </ul>

                                            <div class="grey_box">
                                                <div class="library_filter_sort">
                                                    <div class="library_filter">
                                                        <h3 class="title">Filter by</h3>
                                                        <div class="library_filter_selection">
                                                            <div class="filter_inner">
                                                                <p>Duration</p>
                                                                <div class="filter_select">
                                                                    <select class="form-select min-duration">
                                                                        <option selected>Min</option>
                                                                        <option value="1">10</option>
                                                                        <option value="2">20</option>
                                                                        <option value="3">30</option>
                                                                    </select>
                                                                    <span>to</span>
                                                                    <select class="form-select max-duration" aria-label="Default select example">
                                                                        <option selected>Max</option>
                                                                        <option value="1">40</option>
                                                                        <option value="2">50</option>
                                                                        <option value="3">90</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="filter_inner">
                                                                <p>Style</p>
                                                                <div class="filter_select ">
                                                                    <select class="form-select">
                                                                        <option selected>Krump</option>
                                                                        <option value="1">Krump</option>
                                                                        <option value="2">Krump</option>
                                                                        <option value="3">Krump</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="filter_inner">
                                                                <p>Level</p>
                                                                <div class="filter_select category">
                                                                    <select class="form-select" aria-label="Default select example">
                                                                        <option selected>Intermediate</option>
                                                                        <option value="1">Intermediate1</option>
                                                                        <option value="2">Intermediate2</option>
                                                                        <option value="3">Intermediate3</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="library_sort">
                                                        <h3 class="title">Sort by</h3>
                                                        <div class="filter_inner">
                                                            <div class="filter_select date_purchased">
                                                                <select class="form-select" aria-label="Default select example">
                                                                    <option selected>Date Purchased</option>
                                                                    <option value="1">Date Purchased</option>
                                                                    <option value="2">Date Purchased</option>
                                                                    <option value="3">Date Purchased</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                
                                            <div class="tab-content accordion" id="pills-tabContent">
                                                <div class="tab-pane fade show active accordion-item" id="pills-purchased" role="tabpanel" aria-labelledby="pills-purchased-tab" tabindex="0">
                                                    <h2 class="accordion-header d-md-none" id="headingOne">
                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Purchased</button>
                                                    </h2>

                                                    <div id="collapseOne" class="accordion-collapse collapse show d-md-block" aria-labelledby="headingOne" data-bs-parent="#myTabContent">
                                                        @if(isset($users) && !empty($users))
                                                        @if(count($users) > 0)
                                                        <div class="accordion-body">
                                                            @foreach($users as $key => $user)
                                                            <div class="library_course_box_cover active">
                                                                <?php
                                                                    $parameter =[
                                                                        'id' =>$user->class_id,
                                                                    ];
                                                                   //$parameter= Crypt::encrypt($parameter);
                                                                   $parameter = base64_encode($user->class_id);
                                                                ?>
                                                                <div class="library_course_box">
                                                                    <div class="library_course_box_img">
                                                                        <img src="{{ asset('uploads/dance_class/images/').'/'.$user->class_thumbnail_image }}" alt="library_box_img" />
                                                                    </div>
                                                                    <div class="library_course_box_content">
                                                                        <label class="time">{{$user->duration}}</label>
                                                                        <h2><a href="{{route('mylibrarydetail', ['id' => $parameter])}}">{{$user->class_name}}</a></h2>
                                                                        <?php $user1 = App\Models\MainUser::where('id',$user->user_id)->first(); 
                                                                        $parameter =[
                                                'id' =>$user->id,
                                            ];
                                       //$parameter= Crypt::encrypt($parameter);
                                       $parameter = base64_encode($user->id);
                                                                        ?>

                                                                        <div class="course_author">
                                                                            <img src="{{ asset('uploads/website_users/').'/'.$user1->profile }}" alt="author_img">
                                                                            <p>A course by <a href="{{route('instructorprofile', ['id' => $parameter])}}">{{$user1->name}}</a></p>
                                                                        </div>
                                                                        <div class="progress_bar">
                                                                            <span style="display:block;width:40%;background-color:#FF8200;"></span>
                                                                        </div>
                                                                        <span class="progress_bar_label">2 completed class of 5 available</span>
                                                                        <?php
                                                                    $parameter =[
                                                                        'id' =>$user->class_id,
                                                                    ];
                                                                   //$parameter= Crypt::encrypt($parameter);
                                                                   $parameter = base64_encode($user->class_id);
                                                                ?>
                                                                        <a href="{{route('mylibrarydetail', ['id' => $parameter])}}" class="common_btn">Continue</a>
                                                                    </div>
                                                                </div>    
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        @else
                                                        <div class="col-md-12 col-sm-12 col-lg-12">
                                                            <div class="center">
                                                                <p class="no_content">No Data Found</p>
                                                            </div>    
                                                        </div>
                                                        @endif
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade accordion-item" id="pills-favourite" role="tabpanel" aria-labelledby="pills-favourite-tab">
                                                    <h2 class="accordion-header d-md-none" id="headingTwo">
                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Favourite</button>
                                                    </h2>

                                                    <div id="collapseTwo" class="accordion-collapse collapse show d-md-block" aria-labelledby="headingTwo" data-bs-parent="#myTabContent">
                                                        <div class="accordion-body">
                                                            <div class="library_course_box_cover active">
                                                                <div class="library_course_box">
                                                                    <div class="library_course_box_img">
                                                                        <img src="../assets/frontend/images/listing_popular_img4.jpg" alt="library_box_img" />
                                                                    </div>
                                                                    <div class="library_course_box_content">
                                                                        <label class="time">20min</label>
                                                                        <h2><a href="#">Course title goes here longer name.</a></h2>
                                                                        <div class="course_author">
                                                                            <img src="../assets/frontend/images/author6.png" alt="author_img">
                                                                            <p>A course by <a href="http://localhost/only_dance/public/index.php/instructor-profile/MTA=">Joey</a></p>
                                                                        </div>
                                                                        <div class="progress_bar">
                                                                            <span style="display:block;width:40%;background-color:#FF8200;"></span>
                                                                        </div>
                                                                        <span class="progress_bar_label">2 completed class of 5 available</span>
                                                                        <a href="#" class="common_btn">Continue</a>
                                                                    </div>
                                                                </div>    
                                                            </div>
                                                    
                                                            <div class="library_course_box_cover">
                                                                <div class="library_course_box">
                                                                    <div class="library_course_box_img">
                                                                        <img src="../assets/frontend/images/listing_popular_img1.jpg" alt="library_box_img" />
                                                                    </div>
                                                                    <div class="library_course_box_content">
                                                                        <label class="time">22min</label>
                                                                        <h2><a href="#">Course title goes here longer name.</a></h2>
                                                                        <div class="course_author">
                                                                            <img src="../assets/frontend/images/author2.png" alt="author_img">
                                                                            <p>A course by <a href="http://localhost/only_dance/public/index.php/instructor-profile/MTA=">Jose Portilla</a></p>
                                                                        </div>
                                                                        <div class="progress_bar">
                                                                            <span style="display:block;width:20%;background-color:#FF8200;"></span>
                                                                        </div>
                                                                        <span class="progress_bar_label">1 completed class of 5 available</span>
                                                                        <a href="#" class="common_btn">Start</a>
                                                                    </div>
                                                                </div>    
                                                            </div>

                                                            <div class="library_course_box_cover">
                                                                <div class="library_course_box">
                                                                    <div class="library_course_box_img">
                                                                        <img src="../assets/frontend/images/listing_popular_img2.jpg" alt="library_box_img" />
                                                                    </div>
                                                                    <div class="library_course_box_content">
                                                                        <label class="time">10min</label>
                                                                        <h2><a href="#">Course title goes here longer name.</a></h2>
                                                                        <div class="course_author">
                                                                            <img src="../assets/frontend/images/author2.png" alt="author_img">
                                                                            <p>A course by <a href="http://localhost/only_dance/public/index.php/instructor-profile/MTA=">Kyle Pew</a></p>
                                                                        </div>
                                                                        <div class="progress_bar">
                                                                            <span style="display:block;width:0%;background-color:#FF8200;"></span>
                                                                        </div>
                                                                        <span class="progress_bar_label">You haven’t completed any classes of the course</span>
                                                                        <a href="#" class="common_btn">Start</a>
                                                                    </div>
                                                                </div>    
                                                            </div>

                                                            <div class="library_course_box_cover active">
                                                                <div class="library_course_box">
                                                                    <div class="library_course_box_img">
                                                                        <img src="../assets/frontend/images/listing_popular_img7.jpg" alt="library_box_img" />
                                                                    </div>
                                                                    <div class="library_course_box_content">
                                                                        <label class="time">15min</label>
                                                                        <h2><a href="#">Course title goes here longer name.</a></h2>
                                                                        <div class="course_author">
                                                                            <img src="../assets/frontend/images/author4.png" alt="author_img">
                                                                            <p>A course by <a href="http://localhost/only_dance/public/index.php/instructor-profile/MTA=">Rachel</a></p>
                                                                        </div>
                                                                        <div class="progress_bar">
                                                                            <span style="display:block;width:60%;background-color:#FF8200;"></span>
                                                                        </div>
                                                                        <span class="progress_bar_label">3 completed class of 5 available</span>
                                                                        <a href="#" class="common_btn">Continue</a>
                                                                    </div>
                                                                </div>    
                                                            </div>

                                                            <div class="library_course_box_cover active">
                                                                <div class="library_course_box">
                                                                    <div class="library_course_box_img">
                                                                        <img src="../assets/frontend/images/listing_popular_img9.jpg" alt="library_box_img" />
                                                                    </div>
                                                                    <div class="library_course_box_content">
                                                                        <label class="time">38min</label>
                                                                        <h2><a href="#">Course title goes here longer name.</a></h2>
                                                                        <div class="course_author">
                                                                            <img src="../assets/frontend/images/author6.png" alt="author_img">
                                                                            <p>A course by <a href="http://localhost/only_dance/public/index.php/instructor-profile/MTA=">Joey</a></p>
                                                                        </div>
                                                                        <div class="progress_bar">
                                                                            <span style="display:block;width:80%;background-color:#FF8200;"></span>
                                                                        </div>
                                                                        <span class="progress_bar_label">4 completed class of 5 available</span>
                                                                        <a href="#" class="common_btn">Continue</a>
                                                                    </div>
                                                                </div>    
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="pills-completed" role="tabpanel" aria-labelledby="pills-completed-tab">

                                                </div>

                                                <div class="tab-pane fade" id="pills-inprogress" role="tabpanel" aria-labelledby="pills-inprogress-tab">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--My Account Page-->
        </div>
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
        </script>
@endsection
