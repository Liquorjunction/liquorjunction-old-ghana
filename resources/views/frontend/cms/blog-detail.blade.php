@extends('frontEnd.layouts.new_app')
@section('title','Blog Detail')
@section('content')
    <main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{ route('frontend.home')}}" class="text-grey body-normal">Home</a></li>                    
                    <li><p class="text-black body-normal">Blog Detail</p></li>
                </ul>
            </div>
        </div>
        <?php
                    $date_formate = date('d M Y',strtotime($blogData->created_at));
                    ?>        
        <section class="blog-listing pt-40 py-80">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="blog-detail-img">
                            <img src="{{ asset('uploads/blog/').'/'.$blogData->image }}" alt="solar" title="Solar" />    
                        </div>
                        <div class="blog-detail-content">
                            <p class="blog-detail-date">{{@$date_formate}}</p>
                            <h3>{{@$blogData->title}}</h3>
                            <p>{{@$blogData->short_description}}</p>
                            <p>{!!html_entity_decode(@$blogData->long_description)!!}</p>
                        </div>
                    </div>
                </div>
            </div>            
        </section>
    </main>
@endsection 