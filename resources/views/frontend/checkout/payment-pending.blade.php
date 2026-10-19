@extends('frontend.layouts.app')
@section('title', 'Payment')
@section('content')
    <section class="thank-you py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-12">
                    <div class="thank-you-block">
                        <h1>{{ $title }}</h1>
                        <p>{{ $message }}</p>
                        <a href="{{ url('/') }}" class="solid-button">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
