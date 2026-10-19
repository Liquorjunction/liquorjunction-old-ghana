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
                    <h1>Class List</h1>
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
                    <li class="breadcrumb-item active" aria-current="page">Class List</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--Breadcrumb-->

    <!--Class List Page-->
    
            <!-- <div class="row">
                <div class="col-12 class_list_title">
                    <div class="class_search_cover">
                        <div class="class_search">
                            <h4 class="class_search_title">Search the class</h4>
                            <form role="search" method="get" class="search_form form" action="#" id="search_list_form">
                                <input type="search" class="search-field" onkeyup="fetch_data(this)" id="search_dance_class" name="search" placeholder="Search....">
                                <input type="submit" class="search_submit" value="">
                            </form>
                        </div>
                    </div>
                </div>
            </div> -->
            <section class="common_padding listing_page class_list">
                @include('sweetalert::alert')
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
                            <div class="search_row">
                                    <div class="class_search">
                                       <!--  <h4 class="class_search_title">Search the class</h4> -->
                                        <form role="search" method="get" class="search_form form" action="#" id="search_list_form">
                                            <input type="search" class="search-field" onkeyup="fetch_data(this)" id="search_dance_class" name="search" placeholder="Search....">
                                            <input type="submit" class="search_submit" value="">
                                        </form>
                                    </div>
                                    <a href="{{route('add-class')}}" class="common_btn">Add Class</a>
                            </div>
                            <div class="listing_right">
                                <div class="row" id="search_data">
                                    @if(isset($users) && !empty($users))
                                    @if(count($users) > 0)
                                    @foreach($users as $key => $user)
                                    <div class="col-md-4 col-sm-6">
                                        <?php
                                                $parameter =[
                                                    'id' =>$user->class_id,
                                                ];
                                                //$parameter= Crypt::encrypt($parameter);
                                                $parameter = base64_encode($user->class_id);
                                        ?>
                                        <div class="course_cover_listing">
                                            <div class="list_box">
                                                <div class="list_box_video">
                                                    <a href="{{route('myclass.detail', ['id' => $parameter]) }}">
                                                        <img class="listing_popular_img" src="{{ asset('uploads/dance_class/images/').'/'.$user->class_thumbnail_image }}" alt="listing_popular_img">
                                                        <span class="play_icon">
                                                            <img src="assets/frontend/images/play_icon_small.png" alt="play_icon">
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                            @if(isset($user->user_type) && $user->user_type == 2 || $user->user_type == 3)
                                            <div class="status_label">
                                                <span class="{{ $user->purchase_status == 1 ? 'bg_approved text_approved' : 'bg_pending text_pending'  }}">{{ $user->purchase_status == 1 ? 'Approved' : 'Pending' }}</span>
                                            </div>
                                            @endif
                                            <div class="course_details">
                                                <ul class="course_type">
                                                    <li class="course_box orange_box"><span>{{$user->category_name}}</span></li>
                                                    <li class="course_box yellow_box"><span>{{$user->dance_level_title}}</span></li>
                                                    <li class="course_box blue_box"><span>{{$user->duration}}</span></li>
                                                </ul>
                                                <?php $out = strlen($user->class_name) > 30 ? substr($user->class_name,0,30)."..." : $user->class_name;
                                                ?>
                                                <h3><a href="{{route('myclass.detail', ['id' => $parameter]) }}">{{$out}}</a></h3>
                                                @if(isset($user->user_type) && $user->user_type == 3 )
                                                <div class="total_purchased">
                                                    <h4>Total Purchased :</h4>
                                                    <?php
                                                    $value = \DB::table('class_purchase_history')->where([['class_id',$user->class_id],['user_id',$user->id]])->count();
                                                    ?>
                                                    <span>{{ isset($value) && $value > 0 ? $value : '0'}}</span>
                                                </div>
                                                @endif

                                                <div class="price_edit_delete">
                                                    <ul class="price_nav">
                                                        <li class="main_price">
                                                             <?php 
                                                                $class_price = $user->price;
                                                                $discount = isset($user->discount) ? $user->discount : 0;
                                                                $discount_price = ($class_price * $discount) /100;
                                                                $total_price = $class_price - $discount_price;
                                                            ?>
                                                            <!-- <span>&#8381;</span> -->
                                                            <h4>{{$setting->currency_symbol}} &nbsp;{{$total_price}}</h4>
                                                        </li>
                                                        <li class="offer_price">
                                                            <h4 class="grey_color">&#8381; {{$user->price}}</h4>
                                                        </li>
                                                    </ul>
                                                    <?php
                                                    $parameter =[
                                                        'id' =>$user->class_id,
                                                    ];
                                                       //$parameter= Crypt::encrypt($parameter);
                                                       $parameter = base64_encode($user->class_id);
                                                    ?>
                                                    <ul class="edit_delete">
                                                        <input type="hidden" name="cl_id" value="" id="cl_id">
                                                        <li><a href="{{route('edit-class', ['id' => $parameter]) }}" class="edit_btn"></a></li>
                                                        <li><a href="javascript:void(0)" class="delete_btn" id="delete-class" data-id="{{$user->class_id}}"></a></li>
                                                    </ul>
                                                </div>
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

                                    <!-- <div class="col-md-4 col-sm-6">
                                        <div class="course_cover_listing">
                                            <div class="list_box">
                                                <div class="list_box_video">
                                                    <a href="detail_page.html">
                                                        <img class="listing_popular_img" src="{{ asset('assets/frontend/images/listing_popular_img2.jpg') }}" alt="listing_popular_img">
                                                        <span class="play_icon">
                                                            <img src="{{ asset('assets/frontend/images/play_icon_small.png') }}" alt="play_icon">
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="status_label">
                                                <span class="bg_pending text_pending">Pending</span>
                                            </div>
                                            <div class="course_details">
                                                <ul class="course_type">
                                                    <li class="course_box orange_box"><span>Krump</span></li>
                                                    <li class="course_box green_box"><span>Basic</span></li>
                                                    <li class="course_box blue_box"><span>30min</span></li>
                                                </ul>
                                                <h3><a href="detail_page.html">Class title goes here longer name.</a></h3>
                                                <div class="total_purchased">
                                                    <h4>Total Purchased :</h4>
                                                    <span>550</span>
                                                </div>
                                                <ul class="price_nav">
                                                    <li class="main_price">
                                                        <span>₽</span>
                                                        <h4>500</h4>
                                                    </li>
                                                    <li class="offer_price">
                                                        <h4 class="grey_color">₽ 1100</h4>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>

                                <div class="listing_pagination">
                                    {{ $users->links('vendor.pagination.custom_pagination') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>        
        
    <!--Class List Page-->
</div>
<script>
    $(document).on("click", "#delete-class", function(e) {
                        // alert('hello')
                            e.preventDefault();
                            var link = $(this).attr("href");

                            var id = $(this).attr('data-id');
                            var token = $("meta[name='csrf-token']").attr("content");
                            $('#cl_id').val(id);
                            // alert(link)
                            // return false;
                            Swal.fire({
                              title: 'Delete ?',
                              text: "Are you sure you want to delete class ?",
                              icon: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Yes'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                    $.ajax(
                                        {

                                            url: "{{ url('class/delete') }}" + '/' + id,

                                            type: 'DELETE',

                                            data: {

                                                "id": id,

                                                "_token": token,

                                            },

                                            processData: false,
                                            contentType: false,

                                            success: function (response) 
                                            {
                                                if(response.success == true)
                                                {
                                                    // Swal.fire("Done!", "Your Dance class deleted successfully.", "success");
                                                    var newUrl = response.route;
                                                   

                                                    Swal.fire({
                                                       title: "Done!", 
                                                       text: "Your Dance class deleted successfully.", 
                                                       icon: "success"
                                                    }).then((result) => {
                                                       window.location.href = newUrl;
                                                    });

                                                }
                                                else
                                                {
                                                    Swal.fire("Error!", 'Something went wrong.', "error");

                                                }
                                            },
                                            error:function(err) {
                                                if (err.status == 422)
                                                {
                                                  
                                                    console.log(err);
                                                }
                                            }
                                    });
                              }
                    })
    });

    $("#search_list_form").submit(function(e) {
                    e.preventDefault();
    });
    
    function fetch_data(thisitem) {
        var value = $(thisitem).val();
        $.ajax({
            url: "{{route('myclasssearch')}}",
            method: 'POST',
            data: {
                query: value
            },
            dataType: 'json',
            success: function(data) {
                // $('tbody').html(data.table_data);
                if(data.length > 0)
                {
                    document.getElementById("search_data").innerHTML = data;
                }
                else
                {
                    var html = '<div class="center">';
                    html += '<p class="no_content">No Data Found</p>';
                    html += '</div>';
                    document.getElementById("search_data").innerHTML = html; 
                }
               // document.getElementById("search_data").innerHTML = data;
            }
        })
    }
</script>

@endsection