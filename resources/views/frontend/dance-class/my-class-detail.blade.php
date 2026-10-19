@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
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
    <!--Page Title-->
    <div class="page_title">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>My Class Detail</h1>
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
                    <li class="breadcrumb-item"><a href="{{route('frontend.home')}}"><img src="{{ asset('assets/frontend/images/breadcrumb_od.svg') }}" alt="breadcrumb_od"></a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Class Detail</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--Breadcrumb-->
    <table>
        <tbody>
            <tr>
                <td>

                </td>
            </tr>
        </tbody>
    </table>

    <!--My Class Detail Page-->
    <section class="common_padding detail_page my_class_detail">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="my_class_detail_cover">
                        <div class="class_detail_thumbnail">
                            <img src="{{ asset('uploads/dance_class/images/').'/'.$dance_class->class_thumbnail_image }}" alt="class_detail_thumbnail_img">
                        </div>
                        <div class="my_class_detail_text">
                            <h1>{{$dance_class->class_name}}</h1>
                            <p>A Course by <strong>{{$dance_class->intructore_name}},</strong> A FreeStyle Dancer</p>
                            <ul class="course_type">
                                <li class="course_box orange_box"><span>{{$dance_class->category_name}}</span></li>
                                <li class="course_box yellow_box">
                                    <span>{{$dance_class->dance_level_title}}</span>
                                </li>
                                <li class="course_box blue_box"><span>{{$dance_class->duration}}</span></li>
                            </ul>
                            <h3>Course Price</h3>
                            <ul class="price_nav">
                                <li class="main_price">
                                     <?php 
                                            $class_price = $dance_class->price;
                                            $discount = isset($dance_class->discount) ? $dance_class->discount : 0;
                                            $discount_price = ($class_price * $discount) /100;
                                            $total_price = $class_price - $discount_price;
                                        ?>
                        <!-- <span>&#8381;</span> -->
                                    <h4>{{$setting->currency_symbol}} &nbsp;{{$total_price}}</h4>
                                </li>
                                <li class="offer_price">
                                    <h4 class="grey_color">&#8381; {{$dance_class->price}}</h4>
                                </li>
                            </ul>
                            <?php
                            $value = \DB::table('class_purchase_history')->where([['class_id',$dance_class->id],['user_id',$dance_class->user_id]])->count();
                            ?>
                            <div class="time_purchased">
                                <h3>Total Time Purchased</h3>
                                <span>{{ isset($value) && $value > 0 ? $value : '0'}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
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
                                $levels = \DB::table('class_lession')->where('class_id',$dance_class->id)->get();
                                @endphp
                                @if(isset($levels) && !empty($levels))
                                @foreach($levels as $level)
                                @php
                                $videos = \DB::table('class_lession_video')->where('class_lession_id',$level->id)->get();
                                @endphp
                                @if(isset($videos) && !empty($videos))
                                        @foreach($videos as $video)
                                <div class="intro_cover">
                                    <h3 class="course_intro_title">{{isset($level->title) ? $level->title : ''}}</h3>
                                    <ul class="course_listbox">
                                        
                                        <li>
                                            <div class="intro_left">
                                                <h4><a href="#">{{isset($video->video_name) ? $video->video_name : ''}}</a></h4>
                                                <!-- <p>Duration <span>1:22</span></p> -->
                                            </div>
                                            <div class="intro_right lock_title">
                                                <a href="javascript:(void)" class="play_pause_btn"><img src="{{ asset('assets/frontend/images/play_icon_grey.svg') }}" alt="play_icon_grey" /></a>
                                            </div>
                                        </li>
                                        
                                    </ul>
                                </div>
                                @endforeach
                                        @endif
                                @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="about_class">
                                    <h2>About this class</h2>
                                    <p>{{$dance_class->class_description}}</p>
                                    
                                    <!-- <p>I'm a paragraph. I’m a great place for you to tell I'm a paragraph. I’m a great place for you to tell I'm a paragraph.I'm a paragraph. I’m a great place for you to tell I'm a paragraph. I’m a great place for you to tell I'm a paragraph.I'm a paragraph.</p> -->
                                </div>
                                <div class="class_rating">
                                    <h2>Class Rating</h2>
                                    <div class="rate_box_cover">
                                        <div class="rate_box">
                                            <h3>Most Liked</h3>
                                            <ul>
                                                <li><span class="rate_number">25</span><span class="rate_text">Please add a review</span></li>
                                                <li><span class="rate_number">50</span><span class="rate_text">Please add a review</span></li>
                                                <li><span class="rate_number">75</span><span class="rate_text">Please add a review</span></li>
                                                <li><span class="rate_number">80</span><span class="rate_text">Please add a review</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="review_box">
                                    @if(isset($review) && !empty($review))
                                    <h2>Review</h2>
                                    @foreach($review as $r)
                                    <div class="review_box_content">
                                        <div class="review_circle">
                                            <span>OD</span>
                                        </div>
                                        <div class="review_text">
                                            <?php $user_name = App\Models\MainUser::select('name')->where('id',$r->user_id)->first();?>
                                            <h4>{{$user_name->name}}</h4>
                                            <p>{{$r->text}}</p>
                                            <div class="date_time">
                                                <div class="date_time_label">
                                                    <p>Posted : </p>
                                                    <?php 
                                                        $newDate = date("d-m-Y", strtotime($r->created_at));
                                                        $time = \Helper::converttimeTozone($r->created_at);
                                                        $newTime = explode(' ', $time);
                                                    ?>
                                                    <div class="date_time_post">
                                                        <span class="date">{{$newDate}}</span>
                                                        <span class="time">{{$newTime[1]}} {{$newTime[2]}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                    <!-- <div class="review_box_content">
                                        <div class="review_circle">
                                            <span>OD</span>
                                        </div>
                                        <div class="review_text">
                                            <h4>This is the review text</h4>
                                            <p>I'm a paragraph. I’m a great place for you to tell I'm a paragraph. I’m a great place for you to tell I'm a paragraph.I'm a paragraph.</p>
                                            <div class="date_time">
                                                <div class="date_time_label">
                                                    <p>Posted : </p>
                                                    <div class="date_time_post">
                                                        <span class="date"> 02-09-2022</span>
                                                        <span class="time">12:35:05</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="review_box_content">
                                        <div class="review_circle">
                                            <span>OD</span>
                                        </div>
                                        <div class="review_text">
                                            <h4>This is the review text</h4>
                                            <p>I'm a paragraph. I’m a great place for you to tell I'm a paragraph. I’m a great place for you to tell I'm a paragraph.I'm a paragraph.</p>
                                            <div class="date_time">
                                                <div class="date_time_label">
                                                    <p>Posted : </p>
                                                    <div class="date_time_post">
                                                        <span class="date"> 02-09-2022</span>
                                                        <span class="time">12:35:05</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sidebar">
                        <!-- <div class="rate_review_cover">
                            <div class="rating">
                                <h4>Add ratings</h4>
                                <ul class="star_nav">
                                    <li>
                                        <a href="javascript:void(0)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                <path d="M8.583.556,6.63,4.693l-4.369.666a1.013,1.013,0,0,0-.529,1.706l3.161,3.218-.748,4.546a.966.966,0,0,0,1.388,1.053l3.909-2.147,3.909,2.147a.967.967,0,0,0,1.388-1.053l-.748-4.546,3.161-3.218a1.013,1.013,0,0,0-.529-1.706l-4.369-.666L10.3.556A.94.94,0,0,0,8.583.556Z" transform="translate(-1.441 0.001)" fill="#ffd800"></path>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                <path d="M8.583.556,6.63,4.693l-4.369.666a1.013,1.013,0,0,0-.529,1.706l3.161,3.218-.748,4.546a.966.966,0,0,0,1.388,1.053l3.909-2.147,3.909,2.147a.967.967,0,0,0,1.388-1.053l-.748-4.546,3.161-3.218a1.013,1.013,0,0,0-.529-1.706l-4.369-.666L10.3.556A.94.94,0,0,0,8.583.556Z" transform="translate(-1.441 0.001)" fill="#ffd800"></path>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                <path d="M8.583.556,6.63,4.693l-4.369.666a1.013,1.013,0,0,0-.529,1.706l3.161,3.218-.748,4.546a.966.966,0,0,0,1.388,1.053l3.909-2.147,3.909,2.147a.967.967,0,0,0,1.388-1.053l-.748-4.546,3.161-3.218a1.013,1.013,0,0,0-.529-1.706l-4.369-.666L10.3.556A.94.94,0,0,0,8.583.556Z" transform="translate(-1.441 0.001)" fill="#ffd800"></path>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                <path d="M8.583.556,6.63,4.693l-4.369.666a1.013,1.013,0,0,0-.529,1.706l3.161,3.218-.748,4.546a.966.966,0,0,0,1.388,1.053l3.909-2.147,3.909,2.147a.967.967,0,0,0,1.388-1.053l-.748-4.546,3.161-3.218a1.013,1.013,0,0,0-.529-1.706l-4.369-.666L10.3.556A.94.94,0,0,0,8.583.556Z" transform="translate(-1.441 0.001)" fill="#C4C4C4"></path>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                <path d="M8.583.556,6.63,4.693l-4.369.666a1.013,1.013,0,0,0-.529,1.706l3.161,3.218-.748,4.546a.966.966,0,0,0,1.388,1.053l3.909-2.147,3.909,2.147a.967.967,0,0,0,1.388-1.053l-.748-4.546,3.161-3.218a1.013,1.013,0,0,0-.529-1.706l-4.369-.666L10.3.556A.94.94,0,0,0,8.583.556Z" transform="translate(-1.441 0.001)" fill="#C4C4C4"></path>
                                            </svg>
                                        </a>
                                    </li>

                                </ul>
                            </div>
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
                                <form method="POST" action="{{ route('my-class.review') }}">
                                    <span class="help-block" id="successMessage" style="display:none;">
                                        <span style="color: green; display:none;" id="successMsg" class='validate'></span>
                                    </span>
                                    <span class="help-block" id="errorMessage_classdetail" style="display:none;">
                                        <span style="color: red; display:none;" id="errorMsg" class='validate'></span>
                                    </span>
                                    <input type="hidden" name="class_id" value="{{$dance_class->id}}" id="hidden_class_id">
                                    <div class="form-group">
                                        <textarea rows="2" placeholder="What did you like or dislike?" name="text" id="mytextarea"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <a href="javascript:(void)" class="common_btn" id="submit_btn">Submit</a>
                                    </div>
                                </form>
                            </div>
                        </div> -->
                    </div>
                </div>
                
            </div>
        </div>
    </section>
    <!--My Class Detail Page-->
</div>

<script>
    $("#submit_btn").click(function(e) {

        e.preventDefault();

        var class_id = $("input[name=class_id]").val();
        var text = $("[name='text']").val();
        var url = "{{ route('my-class.review') }}";
        console.log(text);
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                class_id: class_id,
                text: text,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                if (response.success) {
                    $("span#successMessage").css("display", "block");
                    $("span#successMsg").css("display", "block");
                    $("span#successMsg").html("Your review is submitted..!");
                    setTimeout(function() {
                        $('#successMessage').fadeOut('fast');
                    }, 5000);
                    $('textarea#mytextarea').val('');
                } else {
                    $("span#successMessage").css("display", "block");
                    $("span#successMsg").css("display", "block");
                    $("span#successMsg").html("Your review is submitted..!");
                    setTimeout(function() {
                        $('#successMessage').fadeOut('fast');
                    }, 5000);
                    $('textarea#mytextarea').val('');
                }
            },
            error: function(error) {

                var erroJson = JSON.parse(error.responseText);
                for (var err in erroJson) {
                    for (var errstr of erroJson[err])
                        $("span#errorMessage_classdetail").css("display", "block");
                    $("span#errorMsg").css("display", "block");
                    $("span#errorMsg").html(errstr);
                    setTimeout(function() {
                        $('#errorMessage_classdetail').fadeOut('fast');
                    }, 5000);
                    $('textarea#mytextarea').val('');
                }
            }
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
</script>

@endsection