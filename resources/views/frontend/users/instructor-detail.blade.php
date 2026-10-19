@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
        <div class="site_content_cover">
            <!--Page Title-->
                <div class="page_title">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h1>Instructor Profile</h1>
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
                                <li class="breadcrumb-item active" aria-current="page">Instructor Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!--Breadcrumb-->

            <!--Instructor Profile Page-->
                <section class="instructor_profile">
                    <div class="container">
                        <div class="row">
                            @if(isset($users) && !empty($users))
                            @foreach($users as $key => $u)
                            <div class="col-xl-4">
                                <div class="instructor_left">
                                    <div class="instructor_img">
                                        <img src="{{ asset('uploads/website_users/').'/'.$u->profile }}" alt="instructors_img" />
                                    </div>
                                    <div class="insturctor_name">
                                        <h2>{{$u->name}}</h2>
                                        <ul class="course_type">
                                            <?php $category_id = App\Models\DanceClass::select('dance_category_id')->where('user_id', $u->user_id)->where('status', 3)->get(); 
                                            ?>
                                           
                                                @foreach($category_id as $cid)
                                                <?php $category_name = App\Models\DanceCategory::select('category_name')->where('id', $cid->dance_category_id)->where('status', 3)->get(); ?>
                                                @foreach($category_name as $value)
                                                <li class="course_box orange_box"><span>{{$value->category_name}}</span></li>
                                                @endforeach
                                                @endforeach
                                            <li class="course_box yellow_box"><span>{{$u->dance_level_title}}</span></li>
                                            <li class="course_box blue_box"><span>{{$u->duration}}</span></li>
                                        </ul>
                                        <?php $since_date = $u->created_at; 
                                                  $sdate = explode('-',$since_date);
                                        ?>
                                        <span class="time_from">Since {{$sdate[0]}}</span>
                                        <div class="instructor_social">
                                            <ul>
                                                <li><a href="{{$u->instructor_facebook_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/fb_icon_grey.svg') }}" alt="fb_icon_grey"></a></li>
                                                <li><a href="{{$u->instructor_instagram_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/insta_icon_grey.svg') }}" alt="insta_icon_grey"></a></li>
                                                <li><a href="{{$u->instructor_web_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/twitter_icon_grey.svg') }}" alt="twitter_icon_grey"></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="instructor_profile_description">
                                    <p>{{$u->about_me}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="profile_tabs">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <!-- <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">Info</button>
                                        </li> -->
                                        <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="posts" aria-selected="false">Posts</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes" type="button" role="tab" aria-controls="classes" aria-selected="false">Classes</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <!-- <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                            <div class="row align-items-center">
                                                <div class="col-md-12">
                                                    <div class="tab_text">
                                                        <h2>{{$u->name}}</h2>
                                                        <div class="instructor_img">
                                                            <img src="{{ asset('uploads/website_users/').'/'.$u->profile }}" alt="instructors_img">
                                                        </div>
                                                        <p>{{$u->about_me}}</p>
                                                        <ul class="course_type">
                                                            <?php $category = explode(',', $u->category_name)?>
                                                            @foreach($category as $value)
                                                            <li class="course_box orange_box"><span>{{$value}}</span></li>
                                                            @endforeach
                                                            @if($u->dance_level == 1)
                                                            <li class="course_box green_box"><span>Beginner</span></li>
                                                            @elseif($u->dance_level == 2)
                                                            <li class="course_box green_box"><span>Intermediate</span></li>
                                                            @else
                                                            <li class="course_box green_box"><span>Advance</span></li>
                                                            @endif
                                                            <li class="course_box blue_box"><span>{{$u->duration}}</span></li>
                                                        </ul>
                                                        <?php $since_date = $u->created_at; 
                                                                  $sdate = explode('-',$since_date);
                                                        ?>
                                                        <span class="time_from">Since {{$sdate[0]}}</span>
                                                        <ul class="info_social">
                                                            <li><a href="{{$u->instructor_facebook_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/fb_icon_grey.svg') }}" alt="fb_icon_grey"></a></li>
                                                            <li><a href="{{$u->instructor_instagram_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/insta_icon_grey.svg') }}" alt="insta_icon_grey"></a></li>
                                                            <li><a href="{{$u->instructor_web_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/twitter_icon_grey.svg') }}" alt="twitter_icon_grey"></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="posts-tab">
                                            <div class="row">
                                                @if(isset($post) && !empty($post))
                                                @foreach($post as $key => $p)
                                                @if($p->file != null)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="tab_image">
                                                        <img src="{{ asset('uploads/website_users/post/images/').'/'.$p->file }}" alt="dance_img" />
                                                    </div>
                                                </div>
                                                @if($p->video_file != null)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="tab_image">
                                                       <video id="v2" class="video_player" muted controls="controls" add controlsList="nodownload">
                                                            <source src="{{ asset('uploads/website_users/post/videos/').'/'.$u->instruction_video }}" type="video/mp4">
                                                        </video>
                                                    </div>
                                                </div>
                                                @endif
                                                @endif
                                                @endforeach
                                                @endif
                                                <!-- <div class="col-md-4 col-sm-6 tab_video_col">
                                                    <div class="tab_video_cover">
                                                        <div class="tab_video" data-id="1">
                                                            <a href="javascript:(void)"><img class="banner_img" src="{{ asset('assets/frontend/images/banner_img.jpg') }}" alt="banner_img"></a>
                                                            <span class="tab_video_play_icon">
                                                                <img src="{{ asset('assets/frontend/images/play_icon_small.png') }}" alt="tab_video_play_icon">
                                                            </span>
                                                        </div>
                                                        <video id="v1" class="video_player" loop="" autoplay="" muted="" controls="controls">
                                                            <source src="https://praiserun.vrinsoftinc.com/wp-content/uploads/2021/11/Running-27539.mp4" type="video/mp4">
                                                        </video>
                                                    </div>
                                                </div> -->
                                                <!-- <div class="col-md-4 col-sm-6">
                                                    <div class="tab_image">
                                                        <img src="{{ asset('assets/frontend/images/popular_classes_img1.jpg') }}" alt="dance_img" />
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="classes" role="tabpanel" aria-labelledby="classes-tab">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="tab_video_cover">
                                                        <div class="tab_video" data-id="2">
                                                            <!-- <a href="{{route('danceclassdetail')}}"><img class="banner_img" src="{{ asset('assets/frontend/images/banner_img.jpg') }}" alt="banner_img"></a>
                                                            <span class="tab_video_play_icon">
                                                                <img src="{{ asset('assets/frontend/images/play_icon_small.png') }}" alt="tab_video_play_icon">
                                                            </span> -->
                                                        </div>
                                                        @if($u->instruction_video != null)
                                                        <video id="v2" class="video_player" loop autoplay muted controls="controls" add controlsList="nodownload">
                                                            <source src="{{ asset('uploads/dance_class/videos/').'/'.$u->instruction_video }}" type="video/mp4">
                                                        </video>
                                                        @else
                                                        <img src="{{ asset('assets/frontend/images/listing_popular_img4.jpg') }}" alt="dance_img" />
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="video_text">
                                                        <h3>Instructor trailer reel goes here.</h3>
                                                        <p>{{$u->class_description}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif
                                            <div class="row tab_list_row">
                                                @if(isset($user_dance_class) && !empty($user_dance_class))
                                                @foreach($user_dance_class as $key => $udc)
                                                <div class="col-md-4 col-sm-6">
                                                    <?php $dance_category = App\Models\DanceCategory::where('id',$udc->dance_category_id)->first(); ?>
                                                    <?php
                                                            $parameter =[
                                                                'id' =>$udc->class_id,
                                                            ];
                                                       //$parameter= Crypt::encrypt($parameter);
                                                       $parameter = base64_encode($udc->class_id);
                                                    ?>
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href="{{route('danceclassdetailwithid', ['id' => $parameter]) }}">
                                                                    <img class="listing_popular_img" src="{{ asset('uploads/dance_class/images/').'/'.$udc->class_thumbnail_image }}" alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src="{{ asset('assets/frontend/images/play_icon_small.png') }}" alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>{{$dance_category->category_name}}</span></li>
                                                                <li class="course_box yellow_box"><span>{{$udc->dance_level_title}}</span></li>
                                                                <li class="course_box blue_box"><span>{{$udc->duration}}</span></li>
                                                            </ul>
                                                            <?php $out = strlen($udc->class_name) > 30 ? substr($udc->class_name,0,30)."..." : $udc->class_name;
                                                            ?>
                                                            <h3><a href="{{route('danceclassdetailwithid', ['id' => $parameter]) }}">{{$out}}</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <?php 
                                                                        $class_price = $udc->price;
                                                                        $discount = isset($udc->discount) ? $udc->discount : 0;
                                                                        $discount_price = ($class_price * $discount) /100;
                                                                        $total_price = $class_price - $discount_price;

                                                                        $out_1 = strlen($total_price) > 10 ? substr($total_price,0,10)."..." : $total_price;

                                                                        $out_2 = strlen($udc->price) > 10 ? substr($udc->price,0,10)."..." : $udc->price;
                                                                    ?>
                                                                    <span>{{isset($setting) ? $setting->currency_symbol : ''}}</span>
                                                                    <h4>{{$out_1}}</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                    <h4 class="grey_color">{{isset($setting) ? $setting->currency_symbol : ''}} {{$out_2}}</h4>
                                                                </li>
                                                            </ul>
                                                            <?php $user = App\Models\MainUser::where('id',$udc->user_id)->first(); ?>
                                                            <div class="course_author course_author_cover">
                                                                <div class="course_author_left">
                                                                    <img src="{{ asset('uploads/website_users/').'/'.$user->profile }}" alt="author_img">
                                                                    <span>{{$user->name}}</span>
                                                                </div>
                                                                <?php 
                                                                
                                                           
                                                                ?>
                                                                <div class="fav_list">
                                                                    <a href="javascript:(void)">
                                                                        @if($udc->favourite == 1)
                                                                        <img src="{{ asset('assets/frontend/images/heart_icon.svg') }}" alt="heart_icon">
                                                                        @else
                                                                        <img src="{{ asset('assets/frontend/images/heart_icon_border.svg') }}" alt="heart_icon">
                                                                        @endif
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
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--Instructor Profile Page-->
        </div>    
@endsection