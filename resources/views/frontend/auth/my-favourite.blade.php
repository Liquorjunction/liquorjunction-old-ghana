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
                                <h1>My Favourite</h1>
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
                                <li class="breadcrumb-item active" aria-current="page">My Favourite</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!--Breadcrumb-->

            <!--My Favourite Page-->
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
                           <div class="col-lg-9">
                                <div class="listing_right">
                                    <div class="row">
                                        @if(isset($users) && $users != null)
                                        @if(count($users) > 0)
                                        @foreach($users as $key => $user)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="course_cover_listing">
                                                <div class="list_box">
                                                    <div class="list_box_video">
                                                        <?php
                                                            $parameter =[
                                                                    'id' =>$user->class_id,
                                                                ];
                                                           // $parameter= Crypt::encrypt($parameter);
                                                            $parameter = base64_encode($user->class_id);
                                                        ?>
                                                        <a href="{{route('danceclassdetailwithid', ['id' => $parameter]) }}">
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
                                                    <?php $out = strlen($user->class_name) > 30 ? substr($user->class_name,0,30)."..." : $user->class_name;
                                                    ?>
                                                    <h3><a href="{{route('danceclassdetailwithid', ['id' => $parameter]) }}">{{$out}}</a></h3>
                                                    <ul class="price_nav">
                                                         <?php 
                                                            $class_price = $user->price;
                                                            $discount = isset($user->discount) ? $user->discount : 0;
                                                            $discount_price = ($class_price * $discount) /100;
                                                            $total_price = $class_price - $discount_price;
                                                        ?>
                                                        <li class="main_price">
                                                            <!-- <span>&#8381;</span> -->
                                                            <h4>{{$setting->currency_symbol}} &nbsp;{{$total_price}}</h4>
                                                        </li>
                                                        <li class="offer_price">
                                                            <h4 class="grey_color">&#8381; {{$user->price}}</h4>
                                                        </li>
                                                    </ul>
                                                    <?php $user_1 = App\Models\MainUser::where('id',$user->user_id)->first(); ?>
                                                    <div class="course_author">
                                                        <img src="{{ asset('uploads/website_users/').'/'.$user_1->profile }}" alt="author_img">
                                                        <span>{{$user_1->name}}</span>
                                                                                                                        <div class="fav_list">
                                
                                                    </div>
                                                    <a href="javascript:(void)">
                                                                        <img src="{{ asset('assets/frontend/images/heart_icon.svg') }}" alt="heart_icon">
                                                                        
                                                                        <span>Favourite</span>
                                                                    </a>
                                                                </div>

                                                                <!-- <div class='control-group'>
                                                                    <input checked='checked' class='red-heart-checkbox' id='red-check1' type='checkbox'>
                                                                    <label for='red-check1'>
                                                                      Checked
                                                                    </label>
                                                                </div> -->
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @else
                                        <div class="col-md-12 col-sm-12 col-lg-12">
                                            <div class="center">
                                                <p class="no_content">No Data Found</p>
                                            </div>    
                                        </div>
                                        @endif
                                        @endif                                        
                                    </div>

                                    <div class="listing_pagination">
                                        {{ $users->links('vendor.pagination.custom_pagination') }}
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
