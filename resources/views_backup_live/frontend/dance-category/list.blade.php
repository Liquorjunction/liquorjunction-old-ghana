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
                                <h1>Categories</h1>
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
                            <li class="breadcrumb-item active" aria-current="page">Categories</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!--Category Page-->
            <section class="dance_categories">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="section_title">
                                <h2>{{ \Helper::lang_data('categories_subtitle') }}</h2>
                                <p>{{ \Helper::lang_data('categories_subtitle_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if(isset($dance_category) && !empty($dance_category))
                        @if(count($dance_category) > 0)
                        @foreach($dance_category as $key => $dc)
                        <?php
                                $parameter =[
                                    'id' =>$dc->id,
                                ];
                           // $parameter= Crypt::encrypt($parameter);
                            $parameter = base64_encode($dc->id);
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('danceclasslist', ['id' => $parameter]) }}" class="dance_category_cover">
                                <div class="category_img">
                                    <img src="{{ asset('uploads/dance_category/images/').'/'.$dc->photo }}" alt="dance_category_img"/>
                                   <!--  <img src="{{ asset('assets/frontend/images/variety_img1.jpg') }}" alt="dance_category_img"/> -->
                                </div>
                                <?php $out = strlen($dc->description) > 60 ? substr($dc->description,0,60)."..." : $dc->description;
                                ?>
                                <div class="category_details">
                                    <h3>{{$dc->category_name}}</h3>
                                    <p>{{$out}}</p>
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
                                    {{ $dance_category->links('vendor.pagination.custom_pagination') }}
                                </div>
                            </div>
                        </div>
                </div>
            </section>
            <!--Category Page-->
        </div>
        <!-- <video controls autoplay  id="intro_video" muted>
                                    <source src="https://praiserun.vrinsoftinc.com/wp-content/uploads/2021/11/Running-27539.mp4" type="video/mp4">
                                </video> -->
@endsection
