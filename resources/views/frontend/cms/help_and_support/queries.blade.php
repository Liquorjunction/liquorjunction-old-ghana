@php
if(\Session::get('language')==1){
    $page_title = $pageInfo->page_name;
    $page_content = $pageInfo->page_content;
    $photo = $pageInfo->photo;
}else{
    $page_title = ($pageInfo->page_name_fr)?$pageInfo->page_name_fr:$pageInfo->page_name;
    $page_content = ($pageInfo->page_content_fr)?$pageInfo->page_content_fr:$pageInfo->page_content;
    $photo = $pageInfo->photo;
}
@endphp
@extends('frontend.layouts.app')
@section('title',$page_title  )
@section('content')
@if(@$photo)
<section class="title-banner" style="{{ !empty($photo) ? 'background-image: url('.asset('uploads/cms/' . $photo).'); background-repeat: no-repeat; background-size: cover;' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-0">{{ $page_title }}</h1>
            </div>
        </div>
    </div>
</section>
@endif
@include('frontend.cms.help_and_support.breadcrumb')
@include('sweetalert::alert')  
<style>
.error{
    color: red;
}
</style>
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
                    <h3>{{@Helper::language('Queries')}}</h3>
                    <div class="row">
                        <div class="col-lg-6">
                            {!!html_entity_decode($page_content)!!}
                        </div>
                        <div class="col-lg-6">
                            <div class="common-card queries-from">
                                <form action="{{route('queriesStore')}}" method="POST" class="row">
                                    <div class="form-group col-12">
                                        <label for="">{{@Helper::language('name_label_web')}} <span class="error">*</span></label>
                                        <input type="text" name="name" id="name" value="{{old('name')}}" placeholder="{{@Helper::language('enter_name_web')}}"  onkeypress="return onlyString(event)" >
                                        @if ($errors->has('name'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('name') }}</span>
                                        </span>
                                        @endif                                       
                                    </div>                                        
                                    <div class="form-group has-validation">
                                        <label for="">{{@Helper::language('phone_number')}} <span class="error">*</span></label>
                                        <div class="input-group phone-number">
                                            <select class="numbers" name="phone_code">
                                                @foreach ($countryInfo as $value)
                                                <option @if(old('phone_code')==$value->id)? {{'selected'}}@endif  value="{{$value->id}}" >{{$value->phonecode.' ('.$value->shortname.')' }}</option>
                                                @endforeach
                                            </select>
                                            <input type="tel" onkeypress="return restrictInput(this,event, 'digits')" name="phone_number" placeholder="{{@Helper::language('enter_phone_number_place')}}" value="{{old('phone_number')}}">
                                        </div>    
                                        @if ($errors->has('phone_number'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('phone_number') }}</span>
                                        </span>
                                        @endif                                                                             
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="">{{@Helper::language('email_label')}} <span class="error">*</span></label>
                                        <input type="email" placeholder="{{@Helper::language('enter_email_place')}}" name="email" id="email" value="{{old('email')}}">
                                        @if ($errors->has('email'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                                        </span>
                                        @endif 
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="">{{@Helper::language('message_title')}} <span class="error">*</span></label>
                                        <input type="text" name="message_title" placeholder=" {{@Helper::language('enter_message_title')}}" value="{{old('message_title')}}" >
                                        @if ($errors->has('message_title'))
                                        <span class="help-block">
                                            <span style="color: red;" class='validate'>{{ $errors->first('message_title') }}</span>
                                        </span>
                                        @endif 
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="">{{@Helper::language('message_description')}} <span class="error">*</span></label>
                                        <textarea name="message_description" id="" cols="2" rows="5" placeholder="{{@Helper::language('enter_message_description')}}">{{old('message_description')}}</textarea>    
                                        @if ($errors->has('message_description'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('message_description') }}</span>
                                        </span>
                                        @endif                                    
                                    </div>
                                    <div class="form-group col-12">
                                        <label for=""> {{@Helper::language('reason')}} <span class="error">*</span></label>
                                        <select name="reason" id="" class="reason form-select">
                                            <option value="">Select the reason</option>
                                            @if($inquiryReason && count($inquiryReason) > 0)
                                            @foreach ($inquiryReason as $result)
                                            @php
                                                if(\Session::get('language')==1){
                                                    $title = $result->title;
                                                }else{
                                                    $title = ($result->title_fr)?$result->title_fr:$result->title;
                                                }
                                            @endphp
                                                <option @if(old('reason')==$result->id)? {{'selected'}}@endif value="{{$result->id}}">{{$title}}</option>                                            
                                            @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('reason'))
                                        <span class="help-block">
                                            <span  style="color: red;" class='validate'>{{ $errors->first('reason') }}</span>
                                        </span>
                                        @endif 
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="submit" class="solid-button w-100">{{@Helper::language('submit_btn')}}</button>
                                    </div>
                                </form>
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