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

                        @if (isset($earnedpoints) && $earnedpoints > 0)
                            <div class="reward-message mt-4 mb-4 p-4 text-center rounded shadow-sm" style="background: #fff8e1; border-left: 6px solid #fbc02d;">
                                <h5 class="mb-2" style="color: #f57f17;">🎉 You've earned <strong>{{ $earnedpoints }}</strong> reward points!</h5>
                                <p class="mb-0" style="color: #5d4037;">
                                    You can redeem these points on your next purchase for discounts, cash value, or other exciting rewards. 
                                    Keep shopping and keep earning!
                                </p>
                            </div>
                        @endif

                        <p>{{ @Helper::language('thank_you_content') }}</p>
                        <a href="{{ route('frontend.home') }}" class="solid-button">{{ @Helper::language('go_home') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
