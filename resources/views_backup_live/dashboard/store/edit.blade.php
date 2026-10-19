@extends('dashboard.layouts.master')
@section('title', 'Store | Admin Panel')
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

    input[type="time"] {
        cursor: pointer;
    }
</style>
<link href="{{ asset('assets/dashboard/js/iconpicker/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

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

            <h3><i class="material-icons">
                    &#xe02e;</i> {{ __('backend.topicEdit') }} {{ __('backend.store') }}
            </h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / <a href="{{ route('store') }}">{{ __('backend.store_management') }}</a>/
                <span>{{ __('backend.topicEdit') }} {{ __('backend.store') }}</span>
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
            {{ Form::open(['route' => ['store.update', @$StoreDetails->id], 'method' => 'POST', 'files' => true, 'enctype' => 'multipart/form-data', 'id' => 'storeForm']) }}
            <div class="personal_informations">
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.name_of_store') !!} [EN]<span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="store_name" id="store_name" class="form-control" placeholder="Name of store [EN]" value="{{ @$StoreDetails->store_name }}">
                        @if ($errors->has('store_name'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('store_name') }}</span>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.name_of_store') !!} [FR] <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="store_name_fr" id="store_name" class="form-control" placeholder="Name of store [FR]" value="{{ @$StoreDetails->store_name_fr }}">
                        @if ($errors->has('store_name_fr'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('store_name_fr') }}</span>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.store_contact_number') !!} <span class="valid_field">*</span></label>
                    <div class="col-sm-2">
                        <select style="font-size:15px !important;" name="phone_code" id="phone_code" class="form-control">
                            @foreach ($countryData as $value)
                            <option value="{{$value->phonecode}}"  {{ ($StoreDetails->phone_code == $value->phonecode) ? 'selected' : ''}}>+{{$value->phonecode.' ('.$value->shortname.')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-7">
                        <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="{!! __('backend.store_contact_number') !!}"  value="{{ @$StoreDetails->contact_number}}">
                        @if ($errors->has('contact_number'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('contact_number') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.address') !!} <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="{{ @$StoreDetails->address }}">
                        @if ($errors->has('address'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('address') }}</span>
                        </span>
                        @endif
                        <?php
                        // dd(empty(@$StoreDetails)); 
                        ?>
                        @if(@$StoreDetails == "")
                        <input type="hidden" name="property_latitude" id="property_latitude" value="{{ old('property_latitude') }}">
                        <input type="hidden" name="property_longitude" id="property_longitude" value="{{ old('property_longitude') }}">
                        @else
                        <input type="hidden" name="property_latitude" id="property_latitude" value="{{ @$StoreDetails->latitude }}">
                        <input type="hidden" name="property_longitude" id="property_longitude" value="{{ @$StoreDetails->longitude }}">
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 "></label>
                    <div class="col-sm-9">
                        <div id="myMapaaa">

                        </div>
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
                                <div class="col-sm-4">
                                    <label>Opening Time</label>
                                    <input type="time" class="form-control" name="{{ $time->week_name }}_opening_time" id="{{ $time->week_name }}_opening_time" value="{{ date('H:i', strtotime($time->start_time) )}}">
                                </div>
                                <div class="col-sm-4">
                                    <label>Closing Time</label>
                                    <input type="time" class="form-control" name="{{ $time->week_name }}_close_time" id="{{ date('H:i', strtotime($time->week_name)) }}_close_time" value="{{ $time->end_time }}">
                                </div>
                                <div class="col-sm-2">

                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-group row m-t-md">
                    <div class="col-sm-10 pl-0">
                        <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> Update </button>
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
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4') }}&libraries=places&callback=initialize1" async defer></script>
    @push('after-scripts')
    <script type="text/javascript">
        $('#address').on('keyup', function() {
            $('#property_latitude').val('');
            $('#property_longitude').val('');
        });

        $("#address").on('change', function(e) {
            var geocoder = new google.maps.Geocoder();
            var address = $("#address").val();


            if ($(this).val()) {
                show_address_fetching();

                geocoder.geocode({
                    'address': address
                }, function(results, status) {

                    if (status == google.maps.GeocoderStatus.OK) {
                        var latitude = results[0].geometry.location.lat();
                        var longitude = results[0].geometry.location.lng();

                        $('#property_latitude').val(latitude);
                        $('#property_longitude').val(longitude);
                    }

                    hide_address_fetching();
                });
            }
        });

        function show_address_fetching(latitude, longitude) {
            initialize(latitude, longitude);

        }

        function hide_address_fetching() {

        }


        function showmapView(latitude, longitude, flag) {
            // alert('heeloo')
            var address = $("#address").val();
            // alert(latitude)
            // alert(longitude)
            if (address) {
                // alert('show')
                // loader_show();
                initialize(latitude, longitude, flag);

            } else {
                $('#myMapaaa').parents('.col-md-12').hide();
            }
        }
        window.onload = function() {
            initialize1();
        };


        function initialize1() {
            const center = {
                lat: 50.064192,
                lng: -130.605469
            };

            // Create a bounding box with sides ~10km away from the center point
            const defaultBounds = {
                north: center.lat + 0.1,
                south: center.lat - 0.1,
                east: center.lng + 0.1,
                west: center.lng - 0.1,
            };

            var autocomplete = new google.maps.places.Autocomplete($("#address")[0], {
                bounds: defaultBounds,
            });
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                var latitude = place.geometry.location.lat();
                var longitude = place.geometry.location.lng();
                showmapView(latitude, longitude, 0);
                $("#property_latitude").val(latitude);
                $("#property_longitude").val(longitude);
                console.log(place.address_components);
            });
        }

        function initialize(lat, long, flag) {
            // alert(lat)
            // alert(long)
            var map;
            var marker;
            if (lat != '' && long != '') {
                var lat = lat;
                var long = long;
            } else if (lat == '' && lat != undefined && long == '' && long != undefined) {
                var lat = $("#property_latitude").val();
                var long = $("#property_longitude").val();

                if (lat != '' && long != '') {
                    xyz(lat, long);
                } else {
                    const center = {
                        lat: 50.064192,
                        lng: -130.605469
                    };
                    var lat = center.lat;
                    var long = center.lng;
                }
            } else {

                const center = {
                    lat: 50.064192,
                    lng: -130.605469
                };

                var lat = center.lat;
                var long = center.lng;
            }


            // if (!isNaN(lat) && !isNaN(long)) {
            var myLatlng = new google.maps.LatLng(lat, long);
            // }
            // Create a bounding box with sides ~10km away from the center point
            console.log('myLatlng', myLatlng);
            const defaultBounds = {
                north: lat + 0.1,
                south: lat - 0.1,
                east: long + 0.1,
                west: long - 0.1,
            };
            var geocoder = new google.maps.Geocoder();
            var infowindow = new google.maps.InfoWindow();

            var originalMapCenter = new google.maps.LatLng(50.064192, -130.605469);
            const KUWAIT_BOUNDS = {
                east: 52.0997041,
                west: 42.9700654,
                north: 30.8894920,
                south: 27.5340847,
            };

            var mapOptions = {
                zoom: 18,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                componentRestrictions: {
                    country: "kw"
                },
                bounds: defaultBounds,
                useMapTypeControl: false,
                // restriction: {
                //     latLngBounds: KUWAIT_BOUNDS,
                //     strictBounds: false,
                // },
            };

            // map.setMapTypeId('terrain');
            // var map = new google.maps.Map(document.getElementById('map_with_pin'), {
            //     zoom: 7,
            //     useMapTypeControl: false,
            //     center: originalMapCenter,
            //     restriction: {
            //         latLngBounds: KUWAIT_BOUNDS,
            //         strictBounds: false,
            //     },
            // });

            map = new google.maps.Map(document.getElementById("myMapaaa"), mapOptions);

            marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                draggable: true
            });



            geocoder.geocode({
                'latLng': myLatlng
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        // $('#latitude,#longitude').show();
                        // $('#address').val(results[0].formatted_address);
                        $('#property_latitude').val(marker.getPosition().lat());
                        $('#property_longitude').val(marker.getPosition().lng());
                        infowindow.setContent(results[0].formatted_address);
                        infowindow.open(map, marker);
                        // if (flag != 1) {

                        //     getRegions(marker.getPosition().lat(), marker.getPosition().lng());
                        // }

                    }
                }
            });


            google.maps.event.addListener(marker, 'dragend', function() {
                // alert('helloo')
                $('#property_latitude').val(marker.getPosition().lat());
                $('#property_longitude').val(marker.getPosition().lng());
                xyz(marker.getPosition().lat(), marker.getPosition().lng());
            });

            function xyz(latitude, longitude) {
                // alert('hello')
                // loader_show();
                $.ajax({

                    url: "{{route('getAddress')}}",
                    type: 'post',
                    data: 'latitude=' + latitude + '&longitude=' + longitude,
                    success: function(data) {
                        $("#address").val(data);
                        // loader_hide();
                        initialize(latitude, longitude);
                    }
                });
            }

            $('#myMapaaa').css({
                'display': 'block',
                'width': '100%',
                'height': '350px',
                'border': '0',
                'border-radius': '10px',
                'position': 'relative',
                'overflow': 'hidden',
                'background-color': 'whitesmoke'
            });

            $('#myMapaaa').parents('.col-md-12').css({
                'display': 'block'
            });

        }

        $(document).ready(function() {
            if ($('#address').val()) {
                showmapView($('#property_latitude').val(), $('#property_longitude').val());

            }
        })
    </script>
    @endpush