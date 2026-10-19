@extends('frontend.layouts.app')
@section('title', 'Home')
@section('content')
    @include('sweetalert::alert')
    <section class="thank-you py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-12">
                    <div class="thank-you-block">
                        <span class="thank-you-image">
                            <img src="{{ asset('assets/frontend/images/check-yellow.gif') }}" alt="check gif">
                        </span>
                        <h1>{{ @Helper::language('thank_you') }}</h1>
                        <h6>{{ @Helper::language('thank_you_title') }} @if (isset($orderData))
                                {{ $orderData->order_id }}
                            @endif
                        </h6>
                        <p>{{ @Helper::language('thank_you_content') }}</p>
                        <a href="{{ route('frontend.home') }}" class="solid-button">{{ @Helper::language('go_home') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
