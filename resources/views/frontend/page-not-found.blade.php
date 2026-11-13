@extends('frontend.layouts.app')
@section('title','404')
@section('content')

<div class="content-text">
    <div class="error-page largest-text">
        <div class="image-center">
            <img width="320px" src="{{ asset('assets/frontend/images/404.png')}}" alt="page-not-found">
        </div>
        <h4>404</h4>
        <h2 class="mb-4">{{@Helper::language('something_went_wrong')}}</h2>
        <a href="{{route('frontend.home')}}" class="solid-button">{{@Helper::language('go_home')}}</a>
    </div>
</div>

@endsection