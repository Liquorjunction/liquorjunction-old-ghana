@extends('frontEnd.layouts.new_app')
@section('title','Blog')
@section('content')
@include('sweetalert::alert')
    <main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{ route('frontend.home')}}" class="text-grey body-normal">Home</a></li>                    
                    <li><p class="text-black body-normal">Our Blog</p></li>
                </ul>
            </div>
        </div>        
        <section class="blog-listing pt-40 py-80">
            <div class="container">
                <h1>Our Blog</h1>
                <div class="row">
                    @if($blogData->count() > 0)
                    @foreach($blogData as $blog)
                    <?php
                    $date_formate = date('d M Y',strtotime($blog->created_at));
                    ?>

                    <div class="col-lg-4 col-sm-6 blog-listing-col">
                        <a href="{{route('blogdetails',['id'=>$blog->id])}}" class="blog-box">
                            <div class="blog-img-box">
                                <img src="{{ asset('uploads/blog/').'/'.$blog->image }}" alt="{{@$blog->title}}" title="{{@$blog->title}}" />
                            </div>
                            <div class="blog-detail-box">
                                <p class="mb-2 blog-detail-date">{{@$date_formate}}</p>
                                <h5 class="mb-2">{{@$blog->title}} </h5>
                                <p class="mb-0">{{@$blog->short_description}}</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                    @else
                            <div class="col-lg-12 col-sm-12">
                            <h3 class="text-danger text-center">No data found</h3>
                        </div>
                        @endif
                                                        
                </div>
                {{ $blogData->links('vendor.pagination.custom_pagination') }} 
                <!-- <ul class="pagination mb-0">
                    <li class="prev disabled"><a href="">Prev</a></li>
                    <li><a href="" class="active">01</a></li>
                    <li><a href="">02</a></li>
                    <li><a href="">03</a></li>
                    <li><a href="">04</a></li>
                    <li><a href="">05</a></li>
                    <li class="inbetween">....</li>
                    <li><a href="">24</a></li>
                    <li class="next"><a href="">Next</a></li>
                </ul> -->
            </div>            
        </section>
    </main>
@endsection