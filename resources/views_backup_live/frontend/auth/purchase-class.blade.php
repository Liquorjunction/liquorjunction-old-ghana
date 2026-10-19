@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>Purchased Classes</h1>
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
                                <li class="breadcrumb-item active" aria-current="page">Purchased Classes</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!--Breadcrumb-->

            <!--Purchased Classes Page-->
                <section class="my_account">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="account_sidebar">
                                    <ul>
                                        @if(isset(auth()->guard('main_user')->user()->id) && auth()->guard('main_user')->user()->id > 0)
                                        <li class="{{ request()->is('profile') ? 'active' : '' }}"><a href="{{route('websiteprofile')}}">My Account</a></li>
                                        <li class="{{ request()->is('my-library') ? 'active' : '' }}"><a href="{{route('mylibrary')}}">My Library</a></li>
                                        <li class="{{ request()->is('purchase-class') ? 'active' : '' }}"><a href="{{route('purchase-class')}}">Purchased Classes</a></li>
                                        <li class=""><a href="#">Payment History</a></li>
                                        <li class="{{ request()->is('my-favourite') ? 'active' : '' }}"><a href="{{route('my-favourite')}}">My Favourite</a></li>
                                       <!--  <li><a href="registration.html">Log Out</a></li> -->
                                        <li><a id="logout" href="{{ url('/logout') }}">Logout</a><form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @if(auth()->guard('main_user')->user()->user_type == 3)
                           <div class="col-lg-9">
                                <div class="listing_right">
                                    <div class="row">
                                        @foreach($users as $key => $user)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="course_cover_listing">
                                                <div class="list_box">
                                                    <div class="list_box_video">
                                                        <a href="#">
                                                            <img class="listing_popular_img" src="{{ asset('uploads/dance_class/images/').'/'.$user->class_thumbnail_image }}" alt="listing_popular_img">
                                                            <span class="play_icon">
                                                                <img src="assets/frontend/images/play_icon_small.png" alt="play_icon">
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="course_details">
                                                    <ul class="course_type">
                                                        <li class="course_box orange_box"><span>{{$user->category_name}}</span></li>
                                                        <li class="course_box yellow_box"><span>{{$user->dance_level_title}}</span></li>
                                                        <li class="course_box blue_box"><span>{{$user->duration}}</span></li>
                                                    </ul>
                                                    <h3><a href="#">{{$user->class_name}}</a></h3>
                                                    <ul class="price_nav">
                                                        <li class="main_price">
                                                           <!--  <span>&#8381;</span> -->
                                                            <h4>{{$setting->currency_symbol}} &nbsp;{{$user->price}}</h4>
                                                        </li>
                                                        <li class="offer_price">
                                                            <h4 class="grey_color">{{$setting->currency_symbol}} 1500</h4>
                                                        </li>
                                                    </ul>
                                                    <div class="course_author">
                                                        <img src="{{ asset('uploads/website_users/').'/'.$user->profile }}" alt="author_img">
                                                        <span>{{$user->name}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="listing_pagination">
                                       {{ $users->links('vendor.pagination.custom_pagination') }}
                                    </div>
                                </div>
                            </div>
                            @else
                           <div class="col-lg-9">
                                <div class="listing_right">
                                    <div class="row">
                                        @foreach($users as $key => $user)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="course_cover_listing">
                                                <div class="list_box">
                                                    <div class="list_box_video">
                                                        <a href="#">
                                                            <img class="listing_popular_img" src="{{ asset('uploads/dance_class/images/').'/'.$user->class_thumbnail_image }}" alt="listing_popular_img">
                                                            <span class="play_icon">
                                                                <img src="assets/frontend/images/play_icon_small.png" alt="play_icon">
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="course_details">
                                                    <ul class="course_type">
                                                        <li class="course_box orange_box"><span>{{$user->category_name}}</span></li>
                                                        <li class="course_box yellow_box"><span>{{$user->dance_level_title}}</span></li>
                                                        <li class="course_box blue_box"><span>{{$user->duration}}</span></li>
                                                    </ul>
                                                    <h3><a href="#">{{$user->class_name}}</a></h3>
                                                    <ul class="price_nav">
                                                        <li class="main_price">
                                                            <!-- <span>&#8381;</span> -->
                                                            <h4>{{$setting->currency_symbol}} &nbsp;{{$user->price}}</h4>
                                                        </li>
                                                        <li class="offer_price">
                                                            <h4 class="grey_color">{{$setting->currency_symbol}} 1500</h4>
                                                        </li>
                                                    </ul>
                                                    <?php $user1 = App\Models\MainUser::where('id',$user->user_id)->first(); ?>
                                                    <div class="course_author">
                                                        <img src="{{ asset('uploads/website_users/').'/'.$user1->profile }}" alt="author_img">
                                                        <span>{{$user1->name}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="listing_pagination">
                                        {{ $users->links('vendor.pagination.custom_pagination') }}
                                    </div>
                                </div>
                            </div>
                            @endif
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
                              text: "Are You Sure You Want to logout ?",
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
