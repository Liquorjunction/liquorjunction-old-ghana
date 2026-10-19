@extends('frontend.layouts.app')
@section('title','Home')
@section('content')
@include('sweetalert::alert')

 <!-- Title Banner -->
 @if($pageInfo->photo)
 <section class="title-banner" style="{{ $pageInfo && $pageInfo->photo ? 'background-image: url('.asset('uploads/cms/' . $pageInfo->photo).');' : '' }} background-repeat: no-repeat; background-size: cover;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-0">{{ @Helper::language('blog') }}</h1>
            </div>
        </div>
    </div>
</section>
@endif
<!-- End Title Banner -->

<div class="bread-crumb-block">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">{{@Helper::language('home')}}</a></li>
                <li class="breadcrumb-item"><a href="#">{{@Helper::language('shopping_with_us')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{@Helper::language('blog')}}</li>
            </ul>
        </nav>
    </div>
</div>

<!-- Blog -->
<section class="blog py-60">
    <div class="container">
        <div class="blog-container">
            <div class="row blog-row" id="">
                @if(isset($blogs) && count($blogs) > 0)
                @foreach ($blogs as $blogs_result )
                @php
                    $blog_title='';                  
                    if(session::get('language')==2){
                        $blog_title = ($blogs_result->title_fr)?$blogs_result->title_fr:$blogs_result->title;
                    }else{
                        $blog_title = $blogs_result->title;
                    } 
                @endphp
                <div class="col-md-4 col-sm-6 blog-col">
                    <div class="blog-box">
                        <div class="blog-image">
                            @if (file_exists(public_path() . '/uploads/blog/'.$blogs_result->image))					
                            <img src="{{ asset('uploads/blog/'.$blogs_result->image) }}" title="{{$blog_title}}" />
                            @else
                            <img src="{{ asset('assets/frontend/images/image-not-avilable.png')}}" title="{{Helper::language('image_not_available')}}" alt="{{Helper::language('image_not_available')}}">
                            @endif  
                        </div>
                        <div class="blog-content">
                            <h5><a href="{{url('blog-details/'.Helper::encodeUrl($blogs_result->id))}}">{{$blog_title}}</a></h5>
                            <span>{!!  @$blogs_result->created_at ? Carbon\Carbon::parse($blogs_result->created_at)->format(env('DATE_FORMAT', 'Y-m-d')) : "-" !!}</span>
                            <a href="{{url('blog-details/'.Helper::encodeUrl($blogs_result->id))}}" class="text-link">{{Helper::language('read_more')}}
                                <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path id="Vector" d="M5.02442 4.9663C5.02536 5.05124 5.01005 5.13552 4.97939 5.21432C4.94872 5.29313 4.90331 5.36489 4.84573 5.42551L1.11855 9.3181C1.00158 9.44027 0.842405 9.5094 0.676054 9.5103C0.509704 9.51119 0.349799 9.44377 0.231517 9.32288C0.113236 9.20198 0.0462661 9.0375 0.0453413 8.86563C0.0444166 8.69375 0.109612 8.52857 0.226586 8.4064L3.51897 4.9744L0.196093 1.57799C0.0930913 1.45508 0.0387609 1.29655 0.0439595 1.13408C0.049158 0.971613 0.113503 0.81718 0.224135 0.701639C0.334766 0.586097 0.483537 0.517959 0.640718 0.510839C0.797899 0.503719 0.951912 0.558141 1.07198 0.663232L4.84084 4.51549C4.95689 4.63508 5.02282 4.79699 5.02442 4.9663Z" fill="#2B2B2B"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>                
            {{-- <div id="loader" class="hidden">
                <h2>Loading...</h2>
            </div> --}}
        </div>
    </div>
</section>
<!-- End Blog -->
@include('frontend.newsletter.newsletter')
@endsection