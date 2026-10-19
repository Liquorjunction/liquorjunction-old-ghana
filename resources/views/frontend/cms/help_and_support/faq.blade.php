@extends('frontend.layouts.app')
@section('title','FAQ' )
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

<section class="contact pt-40 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="contact-sidebar-wrapper">
                    <h3>{{@Helper::language('help_and_support')}}</h3>
                    @include('frontend.cms.help_and_support.sidebar')
                </div>
            </div>
            <div class="col-lg-9 col-md-8">
                <div class="content-inner faq-block">
                    <h3>{{@Helper::language('frequently_asked_questions')}}</h3>
                    <div class="accordion" id="accordionExample">
                        @php
                            $i=1;
                        @endphp
                        @foreach($faqData as $faq)
                        @php
                        if(\Session::get('language')==1){
                            $question = $faq->question_name;
                            $answer = $faq->answer;
                        }else{
                            $question = ($faq->question_name_fr)?$faq->question_name_fr:$faq->question_name;
                            $answer = ($faq->answer_fr)?$faq->answer_fr:$faq->answer;
                        }
                        @endphp
                        <div class="accordion-item">
                            <h5 class="accordion-header" id="heading{{$i}}">
                                <button  class="accordion-button @if($i!=1) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$i}}"  aria-expanded="true" aria-controls="collapse{{$i}}">
                                    {{@$question?:''}}
                                </button>
                            </h5>
                            <div id="collapse{{$i}}" class="accordion-collapse collapse @if($i==1) {{'show'}} @endif" aria-labelledby="heading{{$i}}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    {{@$answer?:''}}
                                </div>
                            </div>
                        </div>
                        @php
                            $i++;
                        @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend.newsletter.newsletter')
@endsection