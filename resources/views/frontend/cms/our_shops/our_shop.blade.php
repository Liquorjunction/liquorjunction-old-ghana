@php
    $page_title = @$pageInfo->page_name ?: 'Our Shops';
    $page_content = @$pageInfo->page_content;
    $photo = @$pageInfo->photo;
@endphp
@extends('frontend.layouts.app')
@section('title', $page_title)
@section('content')
@if(@$photo)
<section class="title-banner" style="{{ $photo ? 'background-image: url('.asset('uploads/cms/' . $photo).'); background-repeat: no-repeat; background-size: cover;' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-0">{{ $page_title }}</h1>
            </div>
        </div>
    </div>
</section>
@endif
<section class="cms shop pt-40 pb-60">
    <div class="container">
        @if(!empty($page_content))
            <div class="shop-title">
                {!! html_entity_decode($page_content) !!}
            </div>
        @endif

        <div class="row shop-row">
            @forelse($stores as $store)
            <div class="col-lg-6 col-sm-6 shop-col">
                <div class="our-shop-box">
                    <div class="our-shop-box-content">
                        <div class="our-shop-content-top">
                            <h5 class="mb-0">{{ $store->store_name }}</h5>
                            @php
                                $mapUrl = '';
                                if (!empty($store->latitude) && !empty($store->longitude)) {
                                    $mapUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $store->latitude . ',' . $store->longitude;
                                } elseif (!empty($store->address)) {
                                    $mapUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($store->address);
                                }
                            @endphp
                            @if($mapUrl)
                            <a class="text-sm" href="{{ $mapUrl }}" target="_blank">
                                <svg fill="none" height="22" viewbox="0 0 22 22" width="22" xmlns="http://www.w3.org/2000/svg">
                                    <g id="location">
                                        <path d="M12.15 19.5513C13.895 17.3905 17.875 12.1536 17.875 9.21204C17.875 5.64445 14.9495 2.75 11.3438 2.75C7.73796 2.75 4.8125 5.64445 4.8125 9.21204C4.8125 12.1536 8.79248 17.3905 10.5375 19.5513C10.956 20.0662 11.7315 20.0662 12.15 19.5513ZM11.3438 11.366C10.143 11.366 9.16667 10.4001 9.16667 9.21204C9.16667 8.02396 10.143 7.05802 11.3438 7.05802C12.5445 7.05802 13.5208 8.02396 13.5208 9.21204C13.5208 10.4001 12.5445 11.366 11.3438 11.366Z" fill="#FBB516" id="Vector"></path>
                                    </g>
                                </svg> Get Direction
                            </a>
                            @endif
                        </div>

                        <div class="our-shop-content-bottom">
                            @if($store->contact_number)
                            <a class="text-sm" href="tel:{{ $store->phone_code ? '+'.$store->phone_code : '' }}{{ $store->contact_number }}">
                                {{ $store->phone_code ? '+'.$store->phone_code.' ' : '' }}{{ $store->contact_number }} (Store)
                            </a>
                            @endif

                            @if(!empty(trim(@$store->email)))
                            <a class="text-sm" href="mailto:{{ trim($store->email) }}">{{ trim($store->email) }}</a>
                            @endif

                            @if($store->address)
                            <address class="text-sm mb-0 w-100">{{ $store->address }}</address>
                            @endif
                        </div>
                    </div>

                    @if($store->timings && $store->timings->count() > 0)
                    <ul class="our-shop-timings">
                        @foreach($store->timings as $timing)
                        <li>
                            <p class="text-sm mb-0">{{ $timing->day_name ?: 'Day '.$timing->week_id }}</p>
                            @php
                                $startTime = $timing->start_time;
                                $endTime = $timing->end_time;
                                $isClosed = ($startTime == '00:00:00' && $endTime == '00:00:00') || (empty($startTime) && empty($endTime));
                            @endphp
                            @if($isClosed)
                                <span class="text-sm red-text">Closed</span>
                            @else
                                <span class="text-sm {{ strtolower($timing->day_name) == 'sunday' ? 'red-text' : '' }}">
                                    {{ date('h.i A', strtotime($startTime)) }} - {{ $endTime == '00:00:00' ? '12.00AM (Mid Night)' : date('h.i A', strtotime($endTime)) }}
                                </span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No stores found at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@include('frontend.newsletter.newsletter')
@endsection
