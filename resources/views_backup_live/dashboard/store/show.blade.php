@extends('dashboard.layouts.master')
@section('title', 'Store | Wholesaler Panel')
@push('after-styles')
    <style type="text/css">
        #blah {
            height: 50% !important;
            width: 25% !important;
        }

        #blah1 {
            height: 50% !important;
            width: 25% !important;
        }
    </style>
    <link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">

    <link rel= "stylesheet"
        href= "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

    <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

    <style type="text/css">
        .error {
            color: red;
            margin-left: 5px;
        }
    </style>
@endpush
@section('content')
    <div class="padding edit-package">
        <div class="box">
            <div class="box-header dker">
                <h3> {{ __('backend.view_store') }}</h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / <a
                        href="{{ route('store') }}">{{ __('backend.store_management') }}</a>/
                    <span>{{ __('backend.view_store') }}</span>
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{ route('cms') }}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                <!-- <form class="cmxform" id="productForm" method="post" action="" autocomplete="off"> -->
                {{ Form::open(['route' => ['store.update', @$StoreDetails->id], 'method' => 'POST', 'files' => true, 'enctype' => 'multipart/form-data', 'id' => 'labelForm']) }}

                <div class="personal_informations">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.name_of_store') !!} [EN]</label>
                        <div class="col-sm-10">
                            <input type="text" name="street_address" id="street_address" class="form-control"
                                value="{{ @$StoreDetails->store_name }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.name_of_store') !!} [FN]</label>
                        <div class="col-sm-10">
                            <input type="text" name="street_address" id="street_address" class="form-control"
                                value="{{ @$StoreDetails->store_name_fr }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{!! __('backend.address') !!} </label>
                        <div class="col-sm-10">
                            <input type="text" name="address" id="address" class="form-control"
                                value="{{ @$StoreDetails->address }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 "></label>
                        <div class="col-sm-10">
                            <input type="hidden" name="latitude" id="property_latitude" value="{{ @$StoreDetails->latitude }}">
                            <input type="hidden" name="longitude" id="property_longitude" value="{{ @$StoreDetails->longitude }}">                       
                            <div id="gmap" style="width:100%;height:400px;"></div>
                        </div>
                    </div>
                    
                    <div class="form-group row weekplanid" id="border" style="border: 1px solid; border-radius: 21px;">
                        <br>
                        <label class="col-sm-2 form-control-label" id="pastekey">{!! __('backend.bussiness_hours') !!}</label>
                        <div class="col-sm-10">
                            <div class="multi-group">
                                @foreach ($StoreTimingWeek as $time)
                                    <div class="form-group row">
                                        <label class="col-sm-2 form-control-label">{{ @$time->name }}</label>
                                        @if ($time->start_time == '00:00:00')
                                            <div class="col-sm-4">
                                                <label>Opening Time</label>
                                                <input type="text" step="3600" pattern="[0-2][0-9]:[0-5][0-9]"
                                                    class="form-control" name="monday_opening_time" id="monday_opening_time"
                                                    value="Close" readonly>
                                            </div>
                                        @else
                                            <div class="col-sm-4">
                                                <label>Opening Time</label>
                                                <input type="text" step="3600" pattern="[0-2][0-9]:[0-5][0-9]"
                                                    class="form-control" name="monday_opening_time" id="monday_opening_time"
                                                    value="{{ date('H:i', strtotime($time->start_time)) }}" readonly>
                                            </div>
                                        @endif

                                        @if ($time->end_time == '00:00:00')
                                            <div class="col-sm-4">
                                                <label>Closing Time</label>
                                                <input type="text" step="3600" pattern="[0-2][0-9]:[0-5][0-9]"
                                                    class="form-control" name="monday_close_time" id="monday_close_time"
                                                    value="Close" readonly>
                                            </div>
                                        @else
                                            <div class="col-sm-4">
                                                <label>Closing Time</label>
                                                <input type="text" step="3600" pattern="[0-2][0-9]:[0-5][0-9]"
                                                    class="form-control" name="monday_close_time" id="monday_close_time"
                                                    value="{{ date('H:i', strtotime($time->end_time)) }}" readonly>
                                            </div>
                                        @endif


                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                    <div class="form-group row m-t-md">
                        <div class="col-sm-10 pl-0">

                            <a href="{{ route('store') }}" class="btn btn-default m-t">
                                <i class="material-icons">
                                    &#xe5cd;</i> {!! __('backend.cancel') !!}
                            </a>
                        </div>
                    </div>

                    </form>
                    <!-- {{ Form::close() }} -->
                </div>
            </div>
        </div>
    @endsection


    @push('after-scripts')

    <script type="text/javascript">
        var dbLate = parseFloat(document.getElementById('property_latitude').value);
        var dbLong = parseFloat(document.getElementById('property_longitude').value);
        console.log('dbLate',dbLate);
        console.log('dbLong',dbLong);
        var myLatLng = null;

        function initMap() {

            if (!isNaN(dbLate) && !isNaN(dbLong)) {
                myLatLng = {
                    lat: parseFloat(dbLate),
                    lng: parseFloat(dbLong)
                };

            } else {

                let lat = localStorage.getItem("lat");
                let lng = localStorage.getItem("lng");

                if (lat && lng) {
                    myLatLng = {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    };
                } else {
                    myLatLng = {
                        lat: 22.72,
                        lng: 85.36
                    };
                }
                // console.log(myLatLng);

                $('#latitude').val(myLatLng.lat);
                $('#longitude').val(myLatLng.lng);
            }

            let map = new google.maps.Map(document.getElementById("gmap"), {
                zoom: 6,
                center: myLatLng,
            });

            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                draggable: false
            });

            google.maps.event.addListener(marker, 'position_changed', function() {
                var lat = marker.getPosition().lat();
                var lang = marker.getPosition().lng();

                $("#latitude").val(lat)
                $("#longitude").val(lang)
            });
        }
        
    </script>
    <script>
    // function myMap() {
    //     var mapProp= {
    //       center:new google.maps.LatLng(51.508742,-0.120850),
    //       zoom:5,
    //     };
    //     var map = new google.maps.Map(document.getElementById("gmap"),mapProp);
    //     }
        </script>
        
    <script type="text/javascript"
     src="https://maps.google.com/maps/api/js?key=AIzaSyDXtEB_xDPmgOhwhFCZZJ7d98TnesX7AVQ&callback=initMap"></script>
    @endpush
