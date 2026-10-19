@extends('frontend.layouts.app')
@section('title',Helper::language('track_your_order') )
@section('content')
@if(@$pageInfo->photo)
<section class="title-banner" style="{{ !empty($pageInfo->photo) ? 'background-image: url('.asset('uploads/cms/' . $pageInfo->photo).'); background-repeat: no-repeat; background-size: cover;' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-0">{{ $pageInfo->page_name }}</h1>
            </div>
        </div>
    </div>
</section>
@endif
@include('frontend.cms.help_and_support.breadcrumb')  
@include('sweetalert::alert')

<section class="contact pt-40 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="contact-sidebar-wrapper">
                    <h3>{{@ucfirst(Helper::language('help_and_support'))}}</h3>
                    @include('frontend.cms.help_and_support.sidebar')
                </div>
            </div>
            <div class="col-lg-9 col-md-8">
                <div class="content-inner">
                    <h3>{{Helper::language('teack_your_order')}}</h3>
                    <form action="{{route('checkOrderStatus')}}" method="POST" class="row">
                        <div class="form-group col-12 col-lg-6">
                            <label for="">{{Helper::language('order_numbe_label')}}</label>
                            <input type="text" name="order_number" placeholder="{{Helper::language('enter_order_number')}}" value="{{old('order_number')}}">
                            @if ($errors->has('order_number'))
                            <span class="help-block">
                                <span style="color: red;" class='validate'>{{ $errors->first('order_number') }}</span>
                            </span>
                            @endif
                        </div>
                        <div class="form-group col-12 col-lg-6"></div>
                        <div class="col-sm-6">
                            <label for=""></label>
                            <button type="submit" class="solid-button w-100">{{Helper::language('check status')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend.newsletter.newsletter')
@endsection