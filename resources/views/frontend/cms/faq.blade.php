@extends('frontEnd.layouts.new_app')
@section('title','Faqs')
@section('content')
@include('sweetalert::alert')

<main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><p class="text-black body-normal">FAQ</p></li>
                </ul>
            </div>
        </div>        
        <section class="faq pt-30 py-80">
            <div class="container">
                <div class="faq-block">
                    <h2 class="text-center text-white mb-0">Frequently Asked Questions</h2>
                    <div class="accordion" id="accordionExample">
                        @foreach($faqsData as $key=>$faq)
                            <div class="accordion-item">
                            <h5 class="accordion-header" id="heading{{$key}}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$key}}" aria-expanded="false" aria-controls="collapseOne">{{@$faq->question_name}}?</button>
                            </h5>
                            <div id="collapse{{$key}}" class="accordion-collapse collapse" aria-labelledby="heading{{$key}}" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <p class="mb-0">{{@$faq->answer}}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
