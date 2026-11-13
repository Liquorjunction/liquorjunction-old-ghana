@extends('frontEnd.layouts.new_app')
@section('title','How it`s Work')
@section('content')
@include('sweetalert::alert')

        <main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><p class="text-black body-normal">How It`s Work</p></li>
                </ul>
            </div>
        </div>        
        <section class="about pt-30 py-80">
            <div class="container">
                <h2>How It`s Work</h2>
                <div class="row pb-80">
                    {!!html_entity_decode($cmsData->page_content)!!}
                </div>
            </div>            
        </section>
    </main>
@endsection
