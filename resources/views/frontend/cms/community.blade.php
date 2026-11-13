@extends('frontEnd.layouts.new_app')
@section('title','Community')
@section('content')
@include('sweetalert::alert')

        <main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="{{route('frontend.home')}}" class="text-grey body-normal">Home</a></li>
                    <li><p class="text-black body-normal">Community</p></li>
                </ul>
            </div>
        </div>        
        <section class="about pt-30 py-80">
            <div class="container">
                <h2>Community</h2>
                <div class="row">
                    <div class="col-lg-6 col-md-8">
                        <ul class="community-list">
                            @foreach($communityData as $community)
                            <?php
                            $order_palce_date = \Helper::converttimeTozone($community->created_at);
                            $order_time = date("h:i A d F,Y",strtotime($order_palce_date));
                            ?>
                            <li>
                                <div class="community-info">
                                    <p class="body-large text-bold text-black">{{@$community->first_name}} {{@$community->last_name}} has purchased {{@$community->product_name}}.</p>
                                    <span class="body-normal">{{@$order_time}}</span>
                                </div>
                            </li>
                            @endforeach
                            
                        </ul>
                    </div>
                </div>  
            </div>            
        </section>
    </main>
@endsection