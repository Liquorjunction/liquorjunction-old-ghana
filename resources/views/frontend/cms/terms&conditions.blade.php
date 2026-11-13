@extends('frontEnd.layouts.new_app')
@section('title','Terms Conditions')
@section('content')
@include('sweetalert::alert')
<main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><p class="text-black body-normal">Terms & Conditions</p></li>
                </ul>
            </div>
        </div>        
        <section class="about pt-30 py-80">
            <div class="container">
                <h2>Terms & Conditions</h2>
                <div class="row pb-80">
                    <div class="col-12">
                        {!!html_entity_decode($cmsData->page_content)!!}
                    </div>
                </div>
            </div>            
        </section>
    </main>
@endsection
