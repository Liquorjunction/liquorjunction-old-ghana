@extends('frontend.layouts.app')
@section('title',Helper::language('my_account_label'))
@section('content')
@include('sweetalert::alert')
 
    <div class="bread-crumb-block">
        <div class="container">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{@Helper::language('my_account_label')}}</li>
            </ul>
        </div>
    </div>
    <section class="account account-main pt-20 pb-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4">
                @include('frontend.layouts.account-sidebar')
                </div>
                <div class="col-lg-9 col-md-8">
                    <h2>{{@Helper::language('my_account_label')}}</h2>
                    <div class="common-card account-information">
                        <div class="account-info">
                            <h4 class="mb-0 title-one fw-normal">{{@Helper::language('account_information_heading')}}</h4>
                            <div class="account-info-button">
                                <a href="{{route('userchange-password')}}" class="solid-button">{{@Helper::language('change_password_btn')}}</a>
                            </div>
                        </div>
                        <div class="account-basic-info">
                            <div class="account-basic-info-heading">
                                <h4 class="mb-0 title-one">{{@Helper::language('basic_information_heading')}}</h4>                                    
                            </div>
                            <ul>
                                <li>
                                    <span class="title-two text-dark-grey d-block mb-0">{{@Helper::language('name_label_web')}}</span>
                                    <label class="title-two text-black d-block mb-0">{{isset($myProfile->first_name) ? $myProfile->first_name :''}}  {{isset($myProfile->last_name) ? $myProfile->last_name :''}}</label>
                                </li>
                                <li>
                                    <span class="title-two text-dark-grey d-block mb-0">{{@Helper::language('email_label')}}</span>
                                    <a href="mailto:{{isset($myProfile->email) ? $myProfile->email :''}}" class="title-two">{{isset($myProfile->email) ? $myProfile->email :''}}</a>
                                </li>
                                <li>
                                    <span class="title-two text-dark-grey d-block mb-0">{{@Helper::language('phone_number')}}</span>
                                    {{-- <a href="{{isset($myProfile->phone) ? $myProfile->phone :''}}" class="title-two">+{{isset($myProfile->phone_code) ? $myProfile->phone_code :''}}&nbsp;{{isset($myProfile->phone) ? $myProfile->phone :''}}</a>
                                     --}}
                                     <a href="{{ isset($myProfile->phone) ? 'tel:+' . $myProfile->phone_code . $myProfile->phone : '' }}" class="title-two">
                                        @if(isset($myProfile->phone))
                                            +{{ isset($myProfile->phone_code) ? $myProfile->phone_code : '' }}&nbsp;{{ $myProfile->phone }}
                                        @endif
                                    </a>
                                </li>
                            </ul>
                            <a href="{{route('edit-profile')}}" class="border-button d-inline-block">{{@Helper::language('edit_info_btn')}}</a>
                        </div>                                                       
                    </div>                        
                </div>
            </div>
        </div>
    </section>
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
 <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
@endsection

 