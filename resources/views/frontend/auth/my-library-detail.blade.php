@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style type="text/css">
.rating {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center;
}

.rating > input{ display:none;}

.rating > label {
  position: relative;
    width: 1em;
    font-size: 3vw;
    color: #FFD600;
    cursor: pointer;
}
.rating > label::before{ 
  content: "\2605";
  position: absolute;
  opacity: 0;
}
.rating > label:hover:before,
.rating > label:hover ~ label:before {
  opacity: 1 !important;
}

.rating > input:checked ~ label:before{
  opacity:1;
}

.rating:hover > input:checked ~ label:before{ opacity: 0.4; }
</style>
        <div class="site_content_cover">
            @if(isset($dance_class) && !empty($dance_class))
            <!--Breadcrumb-->
                <div class="breadcrumb_cover">
                    <div class="container">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od" /></a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Library Detail</li>
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

                                    <video loop autoplay muted controls="controls" add controlsList="nodownload">
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
                                                <?php $userId = isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '';
                                                    $is_purchased = \DB::table('class_purchase_history')->where([['class_id',$dance_class->id],['purchase_user_id',$userId],['status',1]])->first(); ?>
                                                <!-- <a href="javascript:void(0)" onclick="favourite(this)">
                                                <img id="my_favourite" src="{{ isset($is_faourite) && !empty($is_faourite) && $is_faourite->status == 1 ? asset('assets/frontend/images/heart_icon.svg') : asset('assets/frontend/images/no_hart.png') }}" alt="heart_icon" />
                                                    <span>Favourite</span>
                                                </a> -->
                                                <a href="javascript:void(0)" onclick="favourite(this)">

                                                        <div class="heart-icon-box">

                                                            <img class="heart-icon heart-icon-fill" id="my_favourite" src="{{isset($is_faourite) && !empty($is_faourite) && $is_faourite->status == 1 ? asset('assets/frontend/images/heart_icon.svg') : asset('assets/frontend/images/heart_icon_border.svg')}}" alt="heart_icon"/>

                                                            <!-- <img class="heart-icon heart-icon-border" src="{{ asset('assets/frontend/images/heart_icon_border.svg') }}" alt="heart_icon"/> -->

                                                        </div>

                                                        <span>Favourite</span>

                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <?php $user = App\Models\MainUser::where('id',$dance_class->user_id)->first(); ?>
                                <div class="detail_text">
                                    <h2>{{$dance_class->class_name}}</h2>
                                    <p>A Course by <strong>{{$user->name}},</strong> A FreeStyle Dancer</p>
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
                                        <h3>Course Content</h3>
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
                                    <div class="course_introduction">
                                        <div class="intro_cover">
                                            <h3 class="course_intro_title">Course Introduction</h3>
                                            <ul class="course_listbox">
                                                <li>
                                                    <div class="intro_left">
                                                        <h4 class="orange_text">
                                                            <a href="#">Opening Remark</a></h4>
                                                        <!-- <p>Duration <span>1:22</span></p> -->
                                                    </div>
                                                    <div class="intro_right">
                                                        <a href="javascript:(void)" class="play_pause_btn orange_border"><img src="{{ asset('assets/frontend/images/pause_icon.svg') }}" alt="pause_icon"/></a>
                                                    </div>
                                                </li>
                                                <!-- <li> -->
                                                    <!-- <div class="intro_left">
                                                        <h4><a href="#">Warm Up</a></h4>
                                                        <p>Duration <span>10:40</span></p>
                                                    </div> -->
                                                    <!-- <div class="intro_right">
                                                        <a href="javascript:(void)" class="play_pause_btn"><img src="{{ asset('assets/frontend/images/play_icon_grey.svg') }}" alt="play_icon_grey"/></a>
                                                    </div> -->
                                                <!-- </li> -->
                                            </ul>
                                        </div>
                                        @php
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
                                                            <h4><a href="javascript:void(0)" id="video_play_id" data-id="{{$video->id}}">{{isset($video->video_name) ? $video->video_name : ''}}</a></h4>
                                                            <!-- <p>Duration <span>1:22</span></p> -->
                                                        </div>
                                                        <div class="intro_right lock_title">
                                                            <a href="javascript:void(0)" class="play_pause_btn"><img src="{{ asset('assets/frontend/images/play_icon_grey.svg') }}" alt="play_icon_grey" id="video_play_id" data-id="{{$video->id}}"/></a>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                    @endif
                                                </ul>
                                            </div>
                                            @endforeach
                                            @endif
                                        <!-- <div class="intro_cover">
                                            <h3 class="course_intro_title">Lesson 2 </h3>
                                            <ul class="course_listbox">
                                                <li>
                                                    <div class="intro_left">
                                                        <h4><a href="#">Chapter 1</a></h4>
                                                        <p>Duration <span>1:22</span></p>
                                                    </div>
                                                    <div class="intro_right lock_title">
                                                        <a href="javascript:(void)" class="play_pause_btn"><img src="{{ asset('assets/frontend/images/play_icon_grey.svg') }}" alt="play_icon_grey"/></a>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="intro_left">
                                                        <h4><a href="#">Chapter 2</a></h4>
                                                        <p>Duration <span>1:22</span></p>
                                                    </div>
                                                    <div class="intro_right lock_title">
                                                        <a href="javascript:(void)" class="play_pause_btn"><img src="{{ asset('assets/frontend/images/play_icon_grey.svg') }}" alt="play_icon_grey"/></a>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="intro_cover">
                                            <h3 class="course_intro_title lock_title">Lesson 3</h3>
                                            <ul class="course_listbox">
                                                <li>
                                                    <div class="intro_left">
                                                        <h4><a href="#">Chapter 1</a></h4>
                                                        <p>Duration <span>1:22</span></p>
                                                    </div>
                                                    <div class="intro_right lock_title">
                                                        <a href="javascript:(void)" class="play_pause_btn"><img src="{{ asset('assets/frontend/images/play_icon_grey.svg') }}" alt="play_icon_grey"/></a>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="intro_left">
                                                        <h4><a href="#">Chapter 2</a></h4>
                                                        <p>Duration <span>1:22</span></p>
                                                    </div>
                                                    <div class="intro_right lock_title">
                                                        <a href="javascript:(void)" class="play_pause_btn"><img src="{{ asset('assets/frontend/images/play_icon_grey.svg') }}" alt="play_icon_grey"/></a>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div> -->
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
                                            <a href="javascript:(void)" class="follow_btn">Follow
                                                <img src="{{ asset('assets/frontend/images/plus_icon.svg') }}" alt="plus_icon" />
                                            </a>
                                            <ul>
                                                <li><a href="{{$dance_class->instructor_facebook_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/fb_icon_grey.svg') }}" alt="fb_icon_grey"></a></li>
                                                <li><a href="{{$dance_class->instructor_instagram_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/insta_icon_grey.svg') }}" alt="insta_icon_grey"></a></li>
                                                <li><a href="{{$dance_class->instructor_web_link}}" target="_blank"><img src="{{ asset('assets/frontend/images/twitter_icon_grey.svg') }}" alt="twitter_icon_grey"></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="rate_review_cover">
                                        <!-- <div class="rating">
                                            <h4>Add ratings</h4>
                                            <ul class="star_nav">
                                                <li>
                                                    <a href="javascript:void()" class="grey_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon_grey.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                    <a href="javascript:void()" class="yellow_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void()" class="grey_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon_grey.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                    <a href="javascript:void()" class="yellow_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void()" class="grey_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon_grey.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                    <a href="javascript:void()" class="yellow_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void()" class="grey_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon_grey.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                    <a href="javascript:void()" class="yellow_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void()" class="grey_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon_grey.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                    <a href="javascript:void()" class="yellow_star">
                                                        <img src="{{ asset('assets/frontend/images/star_icon.svg') }}" alt="star_icon_grey"/>
                                                    </a>
                                                </li>
                                                
                                            </ul>
                                        </div> -->
                                        <label style="color: #1a1a1a; font-weight: 700; font-size: 16px;">Add Ratings</label>
                                        <div class="rating" style="justify-content: left;">
                                            <input type="radio" name="rating" {{ isset($rating->rate) && $rating->rate == 5 ? 'checked' : '' }}  value="5" id="one"><label for="one">☆</label>
                                            <input type="radio" name="rating" {{ isset($rating->rate) && $rating->rate == 4 ? 'checked' : '' }} value="4" id="two"><label for="two">☆</label>
                                            <input type="radio" name="rating" {{ isset($rating->rate) && $rating->rate == 3 ? 'checked' : '' }} value="3" id="three"><label for="three">☆</label>
                                            <input type="radio" name="rating" {{ isset($rating->rate) && $rating->rate == 2 ? 'checked' : '' }} value="2" id="four"><label for="four">☆</label>
                                            <input type="radio" name="rating" {{ isset($rating->rate) && $rating->rate == 1 ? 'checked' : '' }} value="1" id="five"><label for="five">☆</label>
                                        </div>
                                        <div class="review">
                                            <h4>Add a review</h4>
                                            <form method="POST" action="{{route('myreview')}}">
                                                <span class="help-block" id="successMessage_library" style="display:none;">
                                                    <span  style="color: green; display:none;" id="successMsg" class='validate'></span>
                                                </span>
                                                <span class="help-block" id="errorMessage_library" style="display:none;">
                                                    <span  style="color: red; display:none;" id="errorMsg" class='validate'></span>
                                                </span>
                                                <input type="hidden" name="class_id" value="{{$id}}" id="hidden_class_id">
                                                <div class="form-group">
                                                    <textarea rows="2" placeholder="What did you like or dislike?" name="text" id="mytextarea"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <a href="javascript:(void)" class="common_btn" id="submit_btn">Submit</a>
                                                </div>
                                            </form>
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
        <script type="text/javascript">
            $(document).ready(function(){
              var show_btn=$('#share');
              //$("#testmodal").modal('show');
              
                show_btn.click(function(){
                  $("#myModal").modal('show');
              })


        $("#submit_btn").click(function(e){

                e.preventDefault();

                var class_id = $("input[name=class_id]").val();
                var text = $('textarea#mytextarea').val();
                var url = '{{ route('myreview') }}';

                $.ajax({
                   url:url,
                   method:'POST',
                   dataType: 'json',
                   data:{
                          class_id:class_id,
                          text:text
                        },
                   success:function(response){
                      if(response.success){
                              $("span#successMessage_library").css("display", "block");
                              $("span#successMsg").css("display", "block");
                              $("span#successMsg").html("Your review is submitted..!");
                              setTimeout(function() {
                                  $('#successMessage_library').fadeOut('fast');
                                }, 5000);
                              $('textarea#mytextarea').val('');
                      }else{
                              $("span#successMessage_library").css("display", "block");
                              $("span#successMsg").css("display", "block");
                              $("span#successMsg").html("Your review is submitted..!");
                              setTimeout(function() {
                                  $('#successMessage_library').fadeOut('fast');
                                }, 5000);
                              $('textarea#mytextarea').val('');
                      }
                   },
                   error:function(error){
                      
                       var erroJson = JSON.parse(error.responseText);
                          for (var err in erroJson) {
                            for (var errstr of erroJson[err])
                              $("span#errorMessage_library").css("display", "block");
                              $("span#errorMsg").css("display", "block");
                              $("span#errorMsg").html(errstr);
                              setTimeout(function() {
                                  $('#errorMessage_library').fadeOut('fast');
                                }, 5000);
                              $('textarea#mytextarea').val('');
                          }
                   }
                });  
            });

            
        });

        $('input[name=rating]').change(function() {
            var value = $('input[name=rating]:checked').val();
            var class_id = $("input[name=class_id]").val();
           if(value != '' && value != null){
            $.ajax({
                url : "{{ route('my-class.rate') }}",
                type : 'post',
                data : {'class_id' : class_id , 'value' : value },
                success : function (response){
                    if(response.status == 1){
                        if(response.data == 1){
                            $("#five").prop("checked", true);
                        }else if (response.data == 2){
                            $("#four").prop("checked", true);
                        }else if (response.data == 3){
                            $("#three").prop("checked", true);
                        }else if (response.data == 4){
                            $("#two").prop("checked", true);
                        }else{
                            $("#one").prop("checked", true);
                        }
                    }
                }
            })
           }
        });

        $('#video_play_id').click(function() {

                var is_purchased = "{{ isset($is_purchased) ?  '1' : '0' }}"
                var video_id = $('#video_play_id').attr('data-id');
                console.log(video_id);
                // if(video_id != null && is_purchased == 1)
                // {
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
                        }
                   });
                // }
            });

        function favourite(thisitem) {
                var class_id = "{{ base64_decode(Request()->id) }}";
                var login_id = '<?php echo isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : null ?>' 
                
                if ( login_id != '') {
                    $.ajax({
                        url: "{{ route('my-library.favourite.status') }}",
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
        </script>
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