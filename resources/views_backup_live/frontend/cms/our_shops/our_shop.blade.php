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
@section('title',$page_title)
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
<section class="cms shop pt-40 pb-60">
    {!!html_entity_decode($page_content)!!}
</section>
@include('frontend.newsletter.newsletter')
@endsection