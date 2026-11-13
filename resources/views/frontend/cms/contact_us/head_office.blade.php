@extends('frontend.layouts.app')
@section('title',Helper::language('head_office'))
@section('content')
@if(@$pageInfo->photo)
<section class="title-banner" style="background-image: url({{ asset('uploads/cms/' . $pageInfo->photo) }}); background-repeat: no-repeat; background-size: cover;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-0">{{ $pageInfo->page_name }}</h1>
            </div>
        </div>
    </div>
</section>
@endif

@include('frontend.cms.contact_us.breadcrumb')  

<section class="contact pt-40 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="contact-sidebar-wrapper">
                    <h3>{{@Helper::language('contact_us')}}</h3>
                    @include('frontend.cms.contact_us.sidebar')
                </div>
            </div>
            <div class="col-lg-9 col-md-8">
                    <div class="contact-inner">
                        <h3>{{@Helper::language('head_office')}}</h3>
                        <div class="contact-inner-wrapper">
                            <div class="row">
                                <div class="col-lg-6">
                                    <ul class="contact-inner-list">
                                        <li>
                                            <?php 
                                                //  dd($WebmasterSetting);
                                            ?>
                                            <h5>{{@Helper::language('address_label')}}</h5>
                                            <address class="title-two mb-0">{{$WebmasterSetting->address}}</address>
                                        </li>
                                        <li>
                                            <h5>{{@Helper::language('phone')}}</h5>
                                            <a href="tel:{{$WebmasterSetting->phone}}" class="title-two">{{$WebmasterSetting->phone}}</a>
                                        </li>
                                        <li>
                                            <h5>{{@Helper::language('fax')}}</h5>
                                            <a href="javascript:void(0)" class="title-two">{{$WebmasterSetting->fax}}</a>
                                        </li>
                                        <li>
                                            <h5>{{@Helper::language('email_label')}}</h5>
                                            <a href="mailto:{{$WebmasterSetting->email}}" class="title-two">{{$WebmasterSetting->email}}</a>
                                        </li>
                                    </ul>                                        
                                </div>
                                <?php
                                // dd($WebmasterSetting->map_url);
                                ?>
                                <div class="col-lg-6">
                                    <div class="contact-inner-map">
                                        <iframe src="{{$WebmasterSetting->map_url}}" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</section>

@include('frontend.newsletter.newsletter')
@endsection