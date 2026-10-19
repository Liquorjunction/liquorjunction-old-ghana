@extends('frontend.layouts.app')
@section('title',Helper::language('customer_support'))
@section('content')

 <!-- Customer Support -->
 <section class="customer-support py-60">
    <div class="container">
        <div class="customer-support-title">
            <h2 class="text-center">{{@Helper::language('customer_support')}}</h2>
            <p class="grey-text">{{@Helper::language('customer_support_content')}}</p>
        </div>
        <div class="common-card">
            <div class="row customer-support-row">
                <div class="col-lg-4 col-sm-6 customer-support-col">
                    <div class="customer-support-block">
                        <h5 class="mb-12">{{@Str::upper(Helper::language('help_and_support'))}}</h5>
                        <ul class="customer-support-links">
                            <li><a href="{{route('trackOrder')}}">{{@Helper::language('track_your_order')}}</a></li>
                            <li><a href="{{route('faqs')}}">{{@Helper::language('faqs')}}</a></li>
                            <li><a href="{{route('queries')}}">{{@Helper::language('Queries')}}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 customer-support-col">
                    <div class="customer-support-block">                    
                        <h5 class="mb-12">{{@Str::upper(Helper::language('delivery_and_returns'))}}</h5>
                        <ul class="customer-support-links">
                            <li><a href="{{route('deliveryInformation')}}">{{@Helper::language('delivery_information')}}</a></li>
                            <li><a href="{{route('returnsCancellation')}}">{{@Helper::language('returns_and_cancellation')}}</a></li>
                            <li><a href="{{route('damagesWrongGoods')}}">{{@Helper::language('damages_and_wrong_goods')}}</a></li>
                            <li><a href="{{route('ourPackaging')}}">{{@Helper::language('our_packaging')}}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 customer-support-col">
                    <div class="customer-support-block">                    
                        <h5 class="mb-12">{{@Str::upper(Helper::language('contact_us'))}}</h5>
                        <ul class="customer-support-links">
                            <li><a href="{{route('headOffice')}}">{{@Helper::language('head_office')}}</a></li>
                            <li><a href="{{route('orderByPhone')}}">{{@Helper::language('order_by_phone')}}</a></li>
                            <li><a href="{{route('tradeEnquieries')}}">{{@Helper::language('trade_enquiries')}}</a></li>
                            <li><a href="{{route('pressEnquieries')}}">{{@Helper::language('press_enquiries')}}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 customer-support-col">
                    <div class="customer-support-block">                    
                        <h5 class="mb-12">{{@Str::upper(Helper::language('our_shops'))}}</h5>
                        <ul class="customer-support-links">
                            <li><a href="{{route('ourStore')}}">{{@Helper::language('great_portland_street_shop')}}</a></li>
                            <li><a href="{{route('ourStore')}}">{{@Helper::language('covent_garden_shop')}}</a></li>
                            <li><a href="{{route('ourStore')}}">{{@Helper::language('london_bridge_shop')}}</a></li>
                            <li><a href="{{route('ourStore')}}">{{@Helper::language('senchi_street')}}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 customer-support-col">
                    <div class="customer-support-block">                    
                        <h5 class="mb-12">{{@Str::upper(Helper::language('shopping_with_us'))}}</h5>
                        <ul class="customer-support-links">
                            <li><a href="{{route('paymentOption')}}">{{@Helper::language('payment_options')}}</a></li>
                            <li><a href="{{route('placingOrder')}}">{{@Helper::language('placing_your_order')}}</a></li>
                            <li><a href="{{route('securityPrivacy')}}">{{@Helper::language('security_privacy')}}</a></li>
                            <li><a href="{{route('termsCondition')}}">{{@Helper::language('terms_condition')}}</a></li>                                
                            <li><a href="{{route('frontend.blog')}}">{{@Helper::language('lj_blog')}}</a></li>                                
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 customer-support-col">
                    <div class="customer-support-block">                    
                        <h5 class="mb-12">{{@Str::upper(Helper::language('about_us'))}}</h5>
                        <ul class="customer-support-links">
                            <li><a href="{{route('ourCompany')}}">{{@Helper::language('our_company')}}</a></li>
                            <li><a href="{{route('ourHistory')}}">{{@Helper::language('our_history')}}</a></li>
                            <li><a href="{{route('responsibleDrinking')}}">{{@Helper::language('responsible_drinking')}}</a></li>
                            <li><a href="{{route('ourStore')}}">{{@Helper::language('shops')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection