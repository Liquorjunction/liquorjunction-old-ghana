@extends('frontend.layouts.app')
@section('title','Home')
@section('content')
@include('sweetalert::alert')

    <div class="bread-crumb-block">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a  href="{{url('/')}}">{{@Helper::language('home')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{route('frontend.blog')}}">{{@Helper::language('blog')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{@Helper::language('blog_detail')}}</li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Blog -->
    @php        
        $blog_title='';
        $image_not_found = '';
        if(session::get('language')==2){
            $blog_title = ($blog_details->title_fr)?$blog_details->title_fr:$blog_details->title ;
            $blog_short_desp = ($blog_details->short_description_fr)?$blog_details->short_description_fr:$blog_details->short_description;
            $blog_long_desp = ($blog_details->long_description_fr)?$blog_details->long_description_fr:$blog_details->long_description;
        }else{
            $blog_title = $blog_details->title;
            $blog_short_desp = $blog_details->short_description;
            $blog_long_desp = $blog_details->long_description;
        } 
    @endphp
    <section class="blog pt-40 pb-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <span class="blog-detail-date">{!!  @$blog_details->created_at ? Carbon\Carbon::parse($blog_details->created_at)->format(env('DATE_FORMAT', 'Y-m-d')) : "-" !!}</span>
                    <h2>{{$blog_title}}</h2>
                    <div class="blog-detail-img">
                        @if (file_exists(public_path() . '/uploads/blog/'.$blog_details->image))					
                        <img src="{{ asset('uploads/blog/'.$blog_details->image) }}" title="{{$blog_title}}" />
                        @else
                        <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                        @endif  
                    </div>
                    <div class="blog-detail-content">
                        <p>{{$blog_short_desp }}</p>
                        <p>{{strip_tags($blog_long_desp)}}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Blog -->

    @include('frontend.newsletter.newsletter')
    @endsection