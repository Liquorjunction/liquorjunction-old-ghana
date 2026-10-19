@php
if(\Session::get('language')==1){
    $page_title = $pageInfo->page_name;
    $page_content = $pageInfo->page_content;
    $photo = $pageInfo->photo;
}else{
    $page_title = ($pageInfo->page_name_fr)?$pageInfo->page_name_fr:$pageInfo->page_name;
    $page_content = ($pageInfo->page_content_fr)?$pageInfo->page_content_fr:$pageInfo->page_content;
    $photo = $pageInfo->photo;
}
@endphp
@extends('frontend.layouts.app')
@section('title',$page_title )
@section('content')
@if(@$photo)
<section class="title-banner" style="{{ $photo ? 'background-image: url('.asset('uploads/cms/' . $photo).'); background-repeat: no-repeat; background-size: cover;' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-0">{{ $page_title }}</h1>
            </div>
        </div>
    </div>
</section>
@endif
@include('frontend.cms.delivery_and_returns.breadcrumb')  

<section class="contact pt-40 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="contact-sidebar-wrapper">
                    <h3>{{@Helper::language('delivery_and_returns')}}</h3>
                    @include('frontend.cms.delivery_and_returns.sidebar')
                </div>
            </div>
            
            <div class="col-lg-9 col-md-8">
                <div class="content-inner">
                    <h3>{{@$page_title?:''}}</h3>
                    <div class="row">
                        <div class="col-12">
                            {!!html_entity_decode($page_content)!!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend.newsletter.newsletter')
@endsection