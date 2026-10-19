@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.2/plyr.css" />
<style type="text/css">
    a
    {
        cursor: auto !important,

    }
    
    a:hover
    {
        color: black !important,
        
    }
</style>
</style>
        <div class="site_content_cover">
            @if(isset($dance_class) && !empty($dance_class))
            <!--Breadcrumb-->
                <div class="breadcrumb_cover">
                    <div class="container">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od" /></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('dancecategory')}}">Category</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('danceclassdetailwithid',base64_encode($dance_class->id))}}">{{$dance_class->category_name}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{$dance_class->class_name}}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            <!--Breadcrumb-->

            <!--Detail Page-->
            <section class="common_padding detail_page">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="detail_cover">
                                <div class="banner_right_video detail_banner" id="div_video">
                                    <div class="banner_video">
                                        <!-- <a href="javascript:(void)"><img class="banner_img" src="{{ asset('assets/frontend/images/banner_img.jpg') }}" alt="banner_img"/></a>
                                        <span class="video_icon">
                                            <img src="{{ asset('assets/frontend/images/play_icon.png') }}" alt="play_icon">
                                        </span> -->
                                    </div>

                                    <video muted class="js-player" id="player" controls="controls" add controlsList="nodownload" oncontextmenu="return false" id="vid">
                                        <source src="{{ asset('uploads/dance_class/videos/').'/'.$dance_class->instruction_video }}" type="video/mp4">
                                    </video>
                                </div>
                                <div class="detail_label_share">
                                    <div class="detail_label">
                                        <ul class="detail_nav">
                                            <li class="lable_box pink_box">
                                                <img src="{{ asset('assets/frontend/images/fire_icon.svg') }}" alt="fire_icon" />
                                                <span>Bestseller</span>
                                            </li>
                                            <li class="lable_box green_box">
                                                <img src="{{ asset('assets/frontend/images/bar_graph.svg') }}" alt="bar_graph" />
                                                <span>{{$dance_class->dance_level_title}}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="share_favourite">
                                        <ul class="share_favourite_nav">
                                            <li>
                                                <a href="javascript:void(0)" id="share">
                                                    <img src="{{ asset('assets/frontend/images/share_icon.svg') }}" alt="share_icon" />
                                                    <span>Share</span>
                                                </a>
                                            </li>
                                            <li>
                                                <!-- <a href="javascript:void(0)" onclick="favourite(this)">
                                                <img id="my_favourite" src="{{ isset($is_faourite) && !empty($is_faourite) && $is_faourite->status == 1 ? asset('assets/frontend/images/heart_icon.svg') : asset('assets/frontend/images/no_hart.png') }}" alt="heart_icon" />

                                                    <span>Favourite</span> -->

                                                    <a href="javascript:void(0)" onclick="favourite(this)">

                                                        <div class="heart-icon-box">

                                                            <img class="heart-icon heart-icon-fill" id="my_favourite" src="{{isset($is_faourite) && !empty($is_faourite) && $is_faourite->status == 1 ? asset('assets/frontend/images/heart_icon.svg') : asset('assets/frontend/images/heart_icon_border.svg')}}" alt="heart_icon"/>

                                                            <!-- <img class="heart-icon heart-icon-border" src="{{ asset('assets/frontend/images/heart_icon_border.svg') }}" alt="heart_icon"/> -->

                                                        </div>

                                                        <span>Favourite</span>

                                                    </a>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <?php $user = App\Models\MainUser::where('id',$dance_class->user_id)->first(); ?>
                                <div class="detail_text">
                                    <h1>{{$dance_class->class_name}}</h1>
                                    <p>A Course by <strong>{{$dance_class->intructore_name}},</strong> A FreeStyle Dancer</p>
                                </div>
                                <div class="listing_box">
                                    <ul>
                                        <li>
                                            <img src="{{ asset('assets/frontend/images/like_icon.svg') }}" alt="like_icon" />
                                            <h4><span>98%</span> Positive Reviews</h4>
                                        </li>
                                        <li>
                                            <img src="{{ asset('assets/frontend/images/clock.svg') }}" alt="clock_icon" />
                                            <h4><span>1hr 49min </span> Duration</h4>
                                        </li>
                                        <li>
                                            <img src="{{ asset('assets/frontend/images/user.svg') }}" alt="user_icon" />
                                            <h4><span>1591</span> Users Subsribed</h4>
                                        </li>
                                        <li>
                                            <img src="{{ asset('assets/frontend/images/volume.svg') }}" alt="volume_icon" />
                                            <h4><span>English</span> Audio</h4>
                                        </li>
                                        <li>
                                            <img src="{{ asset('assets/frontend/images/creative.svg') }}" alt="creative_icon" />
                                            <h4><span>Spanish, German, Polish, Dutch</span> Subtitles</h4>
                                        </li>
                                    </ul>
                                </div>
                                <div class="list_content">
                                    <h3>Learn the basic principles of {{$dance_class->category_name}}</h3>
                                    <p>{{$dance_class->description}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="sidebar">
                                
                            <div class="course_description">
                            <div class="course_content">
                                <h3>Class Content</h3>
                                <ul>
                                    @php
                                    $total_level = \DB::table('class_lession')->where('class_id',$dance_class->id)->get();
                                    $allLeasons = array_column($total_level->toArray(),'id');
                                    if(isset($allLeasons)){
                                        $total_lectures = DB::table('class_lession_video')->whereIn('class_lession_id',$allLeasons)->get();
                                    }

                                    @endphp
                                    <li>{{isset($total_level) ? count($total_level) : '0'}} sections</li>
                                    <li>{{ isset($total_lectures) ? count($total_lectures) : '0' }} lectures</li>
                                    <li>7h 31m total length</li>
                                </ul>
                                <!-- <a href="javascript:(void)" class="expand_all_btn">Expand all</a> -->
                            </div>
                            <?php 
                                // function getDuration($full_video_path)
                                // {
                                //     $getID3 = new \getID3;
                                //     $file = $getID3->analyze($full_video_path);
                                   
                                //     $playtime_seconds = $file['playtime_seconds'];
                                //     $duration = date('H:i:s.v', $playtime_seconds);

                                //     return $duration;
                                // }

                                // $file = asset('uploads/dance_class/videos/').'/'.$dance_class->instruction_video;
                                // $aa = getDuration($file);
                                
                                // $duration = FFMpeg\FFProbe::create()
                                // ->format($file)
                                // ->get('duration');
                                // dd($duration);

                            //watched video
                            // document.querySelector("video").onended = function() {
                            //   if(this.played.end(0) - this.played.start(0) === this.duration) {
                            //     console.log("Played all");
                            //   }else {
                            //     console.log("Some parts were skipped");
                            //   }
                            // }

                            ?>
                            <div class="course_introduction">
                                <div class="intro_cover">
                                    <h3 class="course_intro_title">Class Introduction</h3>
                                    <ul class="course_listbox">
                                        <li>
                                            <div class="intro_left">
                                                <h4 class="orange_text">
                                                    <a href="#">Opening Remark</a>
                                                </h4>
                                                <!-- <p>Duration <span>1:22</span></p> -->
                                            </div>
                                            <div class="intro_right">
                                                <a href="javascript:(void)" class="play_pause_btn orange_border"><img src="{{ asset('assets/frontend/images/pause_icon.svg') }}" alt="pause_icon" /></a>
                                            </div>
                                        </li>
                                        <!-- <li> -->
                                            <!-- <div class="intro_left">
                                                <h4><a href="#">Warm Up</a></h4>
                                                <p>Duration <span>10:40</span></p>
                                            </div> -->
                                            <!-- <div class="intro_right">
                                                <a href="javascript:(void)" class="play_pause_btn"><img src="{{ asset('assets/frontend/images/play_icon_grey.svg') }}" alt="play_icon_grey" /></a>
                                            </div> -->
                                        <!-- </li> -->
                                    </ul>
                                </div>
                                @php
                                $userId = isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '';
                                $is_purchased = \DB::table('class_purchase_history')->where([['class_id',$dance_class->id],['purchase_user_id',$userId],['status',1]])->first();
                                $is_own_class = \DB::table('class_purchase_history')->where([['class_id',$dance_class->id],['user_id',$userId],['status',1]])->first();
                                $levels = \DB::table('class_lession')->where('class_id',$dance_class->id)->get();
                                @endphp
                                @if(isset($levels) && !empty($levels))
                                @foreach($levels as $level)
                                @php
                                $videos = \DB::table('class_lession_video')->where('class_lession_id',$level->id)->get();
                                @endphp
                                <div class="intro_cover">
                                    <h3 class="course_intro_title">{{isset($level->title) ? $level->title : ''}}</h3>
                                    <ul class="course_listbox">
                                        @if(isset($videos) && !empty($videos))
                                        @foreach($videos as $video)
                                        <li>
                                            <?php 
                                            $path = asset('uploads/class_lession/videos/').'/'.$video->video_file;
                                           // $duration = getDuration($path);
                                            ?>
                                            <div class="intro_left">
                                                <h4><a href="javascript:void(0)" id="video_play_id" data-id="{{$video->id}}">{{isset($video->video_name) ? $video->video_name : ''}}</a>&nbsp;<span id="spanid"></span></h4>
                                                <!-- <span id="spanid"></span> -->
                                            </div>
                                            <div class="intro_right lock_title">
                                                <a href="javascript:void(0)" class="play_pause_btn" data-id="{{$video->id}}"><img src="{{ isset($is_purchased) ? asset('assets/frontend/images/play_icon_grey.svg') : asset('assets/frontend/images/lock_icon.svg') }}" alt="lock_icon" /></a>
                                            </div>
                                        </li>
                                        @endforeach
                                        @endif
                                    </ul>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                                <div class="course_pricing">
                                    <h3>Course Price</h3>
                                    <ul>
                                        <li>
                                             <?php 
                                                $class_price = $dance_class->price;
                                                $discount = isset($dance_class->discount) ? $dance_class->discount : 0;
                                                $discount_price = ($class_price * $discount) /100;
                                                $total_price = $class_price - $discount_price;
                                                $out_1 = strlen($total_price) > 10 ? substr($total_price,0,10)."..." : $total_price;
                                                $out_2 = strlen($dance_class->price) > 10 ? substr($dance_class->price,0,10)."..." : $dance_class->price;
                                            ?>
                                            <div class="price_tag">
                                                <!-- <img src="{{ asset('assets/frontend/images/peso_icon_orange.svg') }}" alt="peso_icon_orange" /> -->
                                                <h4 class="orange_text">{{isset($setting) ? $setting->currency_symbol : '$'}} {{$out_1}}</h4>
                                            </div>

                                            <div class="price_tag">
                                                <!-- <img src="{{ asset('assets/frontend/images/peso_icon_grey.svg') }}" alt="peso_icon_grey" /> -->
                                                <h4 class="grey_text">{{isset($setting) ? $setting->currency_symbol : '$'}} <strike>{{$out_2}}</strike></h4>
                                            </div>
                                            <input type="hidden" name="total_price" value="{{$total_price}}">
                                        </li>
                                    </ul>
                                    <div class="offer_text">
                                        <img src="{{ asset('assets/frontend/images/clock_grey.svg') }}" alt="clock" />
                                        <p>HURRY! The Offer ends in <span>1day 3hr 55min</span></p>
                                    </div>
                                    <div class="offer_btn">
                                        <!-- <a href="javascript:(void)" class="common_btn blue">Buy this Course</a> -->
                                        <!-- <a href="javascript:(void)" class="common_btn transparent">Add to Cart</a> -->
                                        @if(empty($is_purchased) && empty($is_own_class) && $dance_class->class_status == 3)
                                        <a href="javascript:void(0)" onclick="goToCheckOut()" class="common_btn blue">Buy this Course</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="course_tutor">
                                    <div class="tutor_img">
                                        <img src="{{ asset('uploads/website_users/').'/'.$dance_class->profile }}" alt="tutor_img" />
                                    </div>
                                    <div class="tutor_details">
                                        <h4>{{$dance_class->intructore_name}}</h4>
                                        <span>A Course by a freestlye dancer</span>
                                        <div class="tutor_details_inner">
                                            <!-- <a href="javascript:(void)" class="follow_btn">Follow
                                                <img src="{{ asset('assets/frontend/images/plus_icon.svg') }}" alt="plus_icon" />
                                            </a> -->
                                            <ul>
                                                <li><a href="{{$dance_class->instructor_facebook_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/fb_icon_grey.svg') }}" alt="fb_icon_grey"></a></li>
                                                <li><a href="{{$dance_class->instructor_instagram_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/insta_icon_grey.svg') }}" alt="insta_icon_grey"></a></li>
                                                <li><a href="{{$dance_class->instructor_web_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/twitter_icon_grey.svg') }}" alt="twitter_icon_grey"></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="rating">
                                    <h4>Add ratings</h4>
                                    <ul class="star_nav">
                                        <li><a href="javascript:void()"><img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon"/></a></li>
                                        <li><a href="javascript:void()"><img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon"/></a></li>
                                        <li><a href="javascript:void()"><img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon"/></a></li>
                                        <li><a href="javascript:void()"><img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon"/></a></li>
                                        <li><a href="javascript:void()"><img src="{{ asset('assets/frontend/images/star_icon_grey.svg') }}" alt="star_icon"/></a></li>
                                    </ul>
                                </div>
                                <div class="review">
                                    <h4>Add a review</h4>
                                    <form action="">
                                        <div class="form-group">
                                            <textarea rows="2" placeholder="What did you like or dislike?"></textarea>
                                        </div>
                                    </form>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal" role="dialog">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <!-- Header -->
                      <div class="modal-header">
                        <h4 class="modal-title text-body">Share</h4>
                      </div>
                      <!--  Body --> 
                      <div class="modal-body">
                        <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                            <!-- <a class="a2a_dd" href="https://www.addtoany.com/share"></a> -->
                            <a class="a2a_button_facebook"></a>
                            <a class="a2a_button_twitter"></a>
                            <a class="a2a_button_email"></a>
                            <a class="a2a_button_whatsapp"></a>
                            <a class="a2a_button_linkedin"></a>
                        </div>
                            <script async src="https://static.addtoany.com/menu/page.js"></script>
                      </div>
                      <!-- Footer -->
                      <div class="modal-footer">
                        <button type="button" id="close_btn" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div> <!-- Footer End -->
                    </div> <!-- Content end -->
                   </div> <!-- Dialog end -->
               </div>
            </section>
            <!--Detail Page-->
            @endif
        </div>
        <script src="https://cdn.plyr.io/3.7.2/plyr.polyfilled.js"></script>
        <script type="text/javascript">
                document.addEventListener('DOMContentLoaded', () => {
        // Controls (as seen below) works in such a way that as soon as you explicitly define (add) one control
        // to the settings, ALL default controls are removed and you have to add them back in by defining those below.

        // For example, let's say you just simply wanted to add 'restart' to the control bar in addition to the default.
        // Once you specify *just* the 'restart' property below, ALL of the controls (progress bar, play, speed, etc) will be removed,
        // meaning that you MUST specify 'play', 'progress', 'speed' and the other default controls to see them again.

        const controls = [
            'play-large', // The large play button in the center
            'restart', // Restart playback
            'rewind', // Rewind by the seek time (default 10 seconds)
            'play', // Play/pause playback
            'fast-forward', // Fast forward by the seek time (default 10 seconds)
            'progress', // The progress bar and scrubber for playback and buffering
            'current-time', // The current time of playback
            'duration', // The full duration of the media
            'mute', // Toggle mute
            'volume', // Volume control
            'captions', // Toggle captions
            'settings', // Settings menu
            'pip', // Picture-in-picture (currently Safari only)
            'airplay', // Airplay (currently Safari only)
            //'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
            'fullscreen' // Toggle fullscreen
        ];

        const player = Plyr.setup('.js-player', { controls });

    });
            $(document).ready(function(){
                // var vid = document.getElementById("vid");
                // console.log(vid);
                // alert(vid.duration);
              var show_btn=$('#share');
              //$("#testmodal").modal('show');
              
                show_btn.click(function(){
                  $("#myModal").modal('show');
              })

                $("#myModal").modal({backdrop: false});
            });

            var myVideoPlayer = document.getElementById('vid');
               // meta = document.getElementById('meta');
           // console.log(myVideoPlayer.duration);
            myVideoPlayer.addEventListener('loadedmetadata', function () {
                var duration = myVideoPlayer.duration;
                var current_duration = duration.toFixed(2);
                //meta.innerHTML = "Duration is " + duration.toFixed(2) + " seconds."
            });

            function favourite(thisitem) {
                var class_id = "{{ base64_decode(Request()->id) }}";
                var login_id = '<?php echo isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : null ?>' 
                
                if ( login_id != '') {
                    $.ajax({
                        url: "{{ route('favourite.status') }}",
                        type: "post",
                        data: {
                            'login_id': login_id,
                            'id': class_id
                        },
                        success: function(response) {
                            if (response.status == 1) {
                                if (response.is_favourite == 1) {
                                    $(thisitem).find('#my_favourite').attr('src', '<?php echo asset('assets/frontend/images/heart_icon.svg')  ?>');
                                } else {
                                    $(thisitem).find('#my_favourite').attr('src', '<?php echo asset('assets/frontend/images/heart_icon_border.svg')  ?>');
                                }
                            }
                        }
                    });
                }else{
                    window.location.href = "{{ route('websitelogin') }}";
                }

            }

            function goToCheckOut()
            {
                 var class_id = "{{ base64_decode(Request()->id) }}";

                if(class_id != '' && class_id != null )
                {
                     $.ajax({
                         url : "{{ url('/get-class-status') }}" + "/" + class_id,
                         type: 'post',
                         data : {'id' : class_id},
                         success : function(response){
                             var status = response.status;
                             var user_status = response.user.status;
                             var user_1 = response.user_1.status;

                             if(status == 2 || status == 4 || user_status == 2 || user_status == 4 || user_1 == 2 || user_1 == 4)
                             {
                                Swal.fire({
                                      title: 'Error',
                                      text: "Something went wrong",
                                      icon: 'error',
                                      showCancelButton: true,
                                      cancelButtonColor: '#d33',
                                      confirmButtonText: 'Yes'
                                    }).then((result) => {
                                      if (result.isConfirmed) {
                                        window.location.href = "{{route('frontend.home')}}";
                                      }
                                    })
                               // window.location.href = "{{ route('danceclass') }}";
                             }
                             else
                             {
                                $.ajax({
                                     url : "{{route('ajax.check-out')}}",
                                     type: 'post',
                                     data : {'class_id' : class_id},
                                     success : function(response){
                                         // if(response.status == 1){
                                         // window.location.href = "{{ route('dance-class.check-out',['id' => Request()->id]) }}";
                                         // }else{
                                         // window.location.href = "{{ route('websitelogin') }}";
                                         // }

                                         var status = response.status;
                                        
                                         if(status == 2 || status == 4)
                                         {
                                            Swal.fire({
                                                  title: 'Error',
                                                  text: "Something went wrong",
                                                  icon: 'error',
                                                  showCancelButton: true,
                                                  cancelButtonColor: '#d33',
                                                  confirmButtonText: 'Yes'
                                                }).then((result) => {
                                                  if (result.isConfirmed) {
                                                    window.location.href = "{{route('frontend.home')}}";
                                                  }
                                                })
                                           // window.location.href = "{{ route('danceclass') }}";
                                         }
                                         else
                                         {
                                            if(response.status == 1){
                                             window.location.href = "{{ route('dance-class.check-out',['id' => Request()->id]) }}";
                                             }else{
                                                Swal.fire({
                                                      title: 'Error',
                                                      text: "Something went wrong",
                                                      icon: 'error',
                                                      showCancelButton: true,
                                                      cancelButtonColor: '#d33',
                                                      confirmButtonText: 'Yes'
                                                    }).then((result) => {
                                                      if (result.isConfirmed) {
                                                        window.location.href = "{{route('frontend.home')}}";
                                                      }
                                                    })
                                             }
                                         }
                                     } 
                                 });
                             }
                         } 
                     });
                }

            }

            $('#video_play_id').click(function() {

                var is_purchased = "{{ isset($is_purchased) ?  '1' : '0' }}"
                var video_id = $('#video_play_id').attr('data-id');
                console.log(video_id);
                if(video_id != null && is_purchased == 1)
                {
                    $.ajax({
                        url: "{{ url('/play-video') }}" + "/" + video_id,
                        type: "post",
                        data: {
                            'id': video_id
                        },
                        success: function(response) {
                            console.log(response);
                            var data = "{{ asset('uploads/class_lession/videos/') }}" + "/" + response.video_file;

                            console.log(data);
                            // $('#video_id').attr('src',data);
                            // $('#video_id').play();

                            var video = $('#div_video video'),
                            videoSrc = $('source', video).attr('src', data);
                            video[0].load();
                            video[0].play();

                            var v = document.getElementById('vid');
                            v.addEventListener('ended',myfunction,true);
                            function myfunction(e) {
                                // What you want to do after the event
                                if(e.type=="ended") {
                                   // alert("end");
                                    document.getElementById("spanid").innerHTML="&#10004;";
                                }
                            }
                        }
                   });
                }
            });
        </script>

        <!-- //uploads/class_lession/videos -->
        <!-- AddToAny BEGIN -->
        <!-- <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
        <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
        <a class="a2a_button_facebook"></a>
        <a class="a2a_button_twitter"></a>
        <a class="a2a_button_email"></a>
        <a class="a2a_button_whatsapp"></a>
        <a class="a2a_button_linkedin"></a>
        </div>
        <script async src="https://static.addtoany.com/menu/page.js"></script> -->
        <!-- AddToAny END -->    
@endsection