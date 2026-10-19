@php
    if (\Session::get('language') == 1) {
        $page_title = $pageInfo->page_name;
        $page_content = $pageInfo->page_content;
        $photo = $pageInfo->photo;
        // dd($photo);
    } else {
        $page_title = $pageInfo->page_name_fr ? $pageInfo->page_name_fr : $pageInfo->page_name;
        $page_content = $pageInfo->page_content_fr ? $pageInfo->page_content_fr : $pageInfo->page_content;
        $photo = $pageInfo->photo;
        // dd($photo);
    }
@endphp
@extends('frontend.layouts.app')
@section('title', $page_title)
@section('content')

    <!-- Title Banner -->
    @if (@$photo)
        <section class="title-banner"
            style="{{ !empty($photo) ? 'background-image: url(' . asset('uploads/cms/' . $photo) . '); background-repeat: no-repeat; background-size: cover;' : '' }}">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="mb-0">{{ $page_title }}</h1>
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
                    <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">{{ @Helper::language('home') }}</a>
                    </li>
                    @if (Request::route()->getName() != 'aboutUs' && Request::route()->getName() != 'privacyPolicy')
                        <li class="breadcrumb-item"><a href="#">{{ @Helper::language('about_us') }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $page_title }}</li>
                </ul>
            </nav>
        </div>
    </div>
    <section class="contact" style="padding: 30px; 2px;">
        <div class="container">
            <div class="row">
                {!! html_entity_decode($page_content) !!}
            </div>
        </div>
    </section>
    @include('frontend.newsletter.newsletter')
@endsection
