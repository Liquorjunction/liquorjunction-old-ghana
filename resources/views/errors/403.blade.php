@extends('dashboard.layouts.auth')
@section('title', "403")
@section('content')
   <div class="content-text">
            <div class="error-page largest-text">
                <div class="image-center">
                    <img src="{{ asset('assets/dashboard/images/error.png') }}" alt="page-not-found">
                </div>
                <h1>403</h1>
                <h2 class="mb-4">We’ll clean up and try again</h2>
                <a href="{{ route('frontend.home') }}" class="common-btn hvr-radial-out">Go Home</a>
            </div>
        </div>
@endsection

