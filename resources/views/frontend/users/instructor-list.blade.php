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
                                <h1>Instructors</h1>
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
                                <li class="breadcrumb-item active" aria-current="page">Instructors</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            <!--Breadcrumb-->

            <!--Instructors Page-->
            <section class="common_padding instructors">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="section_title">
                                <h2>{{ \Helper::lang_data('instructors_subtitle') }}</h2>
                                <p>{{ \Helper::lang_data('instructors_subtitle_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if(isset($users) && !empty($users))
                        @if(count($users) > 0)
                        @foreach($users as $key => $u)
                        <?php
                                $parameter =[
                                    'id' =>$u->id,
                                ];
                            //$parameter= Crypt::encrypt($parameter);
                            $parameter = base64_encode($u->id);
                        ?>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{route('instructorprofile', ['id' => $parameter])}}" class="instructor_cover">
                                <div class="instructors_img">
                                    <img src="{{ asset('uploads/website_users/').'/'.$u->profile }}" alt="instructors_img"/>
                                </div>
                                <?php $out = strlen($u->about_me) > 30 ? substr($u->about_me,0,30)."..." : $u->about_me;
                                ?>
                                <div class="instructors_details">
                                    <h3>{{$u->name}}</h3>
                                    <p>{{$out}}</p>
                                    <?php $category_id = App\Models\DanceClass::select('dance_category_id')->where('user_id', $u->user_id)->where('status', 3)->groupby('dance_category_id')->get(); 
                                    ?>
                                    <ul>
                                        @foreach($category_id as $cid)
                                        <?php $category_name = App\Models\DanceCategory::select('category_name')->where('id', $cid->dance_category_id)->where('status', 3)->latest()
                                            ->take(2)->get();
                                            dd($category_name); ?>
                                        @foreach($category_name as $value)
                                        <li>{{$value->category_name}}</li>
                                        @endforeach
                                        @endforeach
                                    </ul>
                                </div>
                            </a>
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
                    <div class="row">
                            <div class="col-12">
                                <div class="listing_pagination">
                                    <!-- <ul class="pagination">
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:(void)" aria-label="Previous">
                                                <img class="pagination_left_arrow" src="{{ asset('assets/frontend/images/left_arrow_small.svg') }}" alt="left_arrow_small">
                                            </a>
                                        </li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">1</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">2</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">3</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">4</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">5</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">6</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">...</a></li>
                                        <li class="page-item"><a class="page-link" href="javascript:(void)">10</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="javascript:(void)" aria-label="Next">
                                                <img class="pagination_right_arrow" src="{{ asset('assets/frontend/images/right_arrow_small.svg') }}" alt="right_arrow_small">
                                            </a>
                                        </li>
                                    </ul> -->
                                    {{ $users->links('vendor.pagination.custom_pagination') }}
                                </div>
                            </div>
                        </div>
                </div>
            </section>
            <!--Instructors Page-->
        </div>    
@endsection