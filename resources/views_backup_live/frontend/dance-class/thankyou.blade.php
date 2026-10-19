@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
<style type="text/css">
    .center {
        text-align: center;
        margin-top: 100px;
    }
    .no_content{
        font-weight: bold;
        font-size: xx-large;
    }
</style>
        <div class="site_content_cover">

            <div class="site_content_cover">
                <!--Thank You Page-->
                <section class="common_padding thank_you">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="thank-you-box">
                                    <div class="thank-img">
                                        <img src="{{ asset('assets/frontend/images/icon_success.svg') }}" alt="Success">
                                    </div>
                                    <h1>Thank you for the order</h1>
                                    <p>We are currently processing your order. You can find updates of your order under your orders.</p>
                                    <a class="common_btn" href="{{route('frontend.home')}}">Continue Shopping</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--Thank You Page-->
            </div>
        </div>
@endsection
