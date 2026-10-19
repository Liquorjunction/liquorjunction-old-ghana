@extends('frontEnd.layouts.app')
@section('title','Only Dance')
@section('content')
									@if(isset($dance_class) && !empty($dance_class))
                                    @foreach($dance_class as $key => $dc)
                                    <div class="col-md-4 col-sm-6">
                                        <?php $dance_category = App\Models\DanceCategory::where('id',$dc->dance_category_id)->first(); ?>
                                        <div class="course_cover_listing">
                                            <div class="list_box">
                                                <div class="list_box_video">
                                                    <a href="javascript:(void)">
                                                        <img class="listing_popular_img" src="{{ asset('uploads/dance_class/images/').'/'.$dc->class_thumbnail_image }}" alt="listing_popular_img">
                                                        <span class="play_icon">
                                                            <img src="{{ asset('assets/frontend/images/play_icon_small.png') }}" alt="play_icon">
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="course_details">
                                                <ul class="course_type">
                                                    <li class="course_box orange_box"><span>{{$dance_category->category_name}}</span></li>
                                                    @if($dc->dance_level == 1)
                                                    <li class="course_box yellow_box"><span>Beginner</span></li>
                                                    @elseif($dc->dance_level == 2)
                                                    <li class="course_box yellow_box"><span>Intermediate</span></li>
                                                    @else
                                                    <li class="course_box yellow_box"><span>Advance</span></li>
                                                    @endif
                                                    <li class="course_box blue_box"><span>{{$dc->duration}}</span></li>
                                                </ul>
                                                <h3><a href="javascript:(void)">{{$dc->class_name}}</a></h3>
                                                <ul class="price_nav">
                                                    <li class="main_price">
                                                        <?php 
                                                            $class_price = $dc->price;
                                                            $discount = isset($dc->discount) ? $dc->discount : 0;
                                                            $discount_price = ($class_price * $discount) /100;
                                                            $total_price = $class_price - $discount_price;
                                                        ?>
                                                        <span>{{isset($setting) ? $setting->currency_symbol : ''}}</span>
                                                        <h4>{{$total_price}}</h4>
                                                    </li>
                                                    <li class="offer_price">
                                                         <h4 class="grey_color">{{isset($setting) ? $setting->currency_symbol : ''}} {{$dc->price}}</h4>
                                                    </li>
                                                </ul>
                                                <?php $user = App\Models\MainUser::where('id',$dc->user_id)->first(); ?>
                                                <div class="course_author">
                                                    <img src="{{ asset('uploads/website_users/').'/'.$user->profile }}" alt="author_img" height="20%" width="20%">
                                                    <span>{{$user->name}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
@endsection