@extends('dashboard.layouts.master')
@section('title', 'Store | Admin Panel')
@include('sweetalert::alert')
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.14.0/jquery.timepicker.min.css" integrity="sha512-WlaNl0+Upj44uL9cq9cgIWSobsjEOD1H7GK1Ny1gmwl43sO0QAUxVpvX2x+5iQz/C60J3+bM7V07aC/CNWt/Yw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            <h3>{{ __('backend.new_store') }}</h3>
            <small>
                <a href="{{ route('adminHome') }}">{{ __('backend.dashboard') }}</a> / <a href="{{ route('store') }}">{{ __('backend.store_management') }}</a>/
                <span>{{ __('backend.new_store') }}</span>
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
            {{ Form::open(['route' => ['store.store'], 'method' => 'POST', 'files' => true, 'enctype' => 'multipart/form-data', 'id' => 'storeForm']) }}
            <div class="personal_informations">
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.name_of_store') !!} [EN] <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="store_name" id="store_name" class="form-control" placeholder="Name of store [EN]" value="{{ old('store_name') }}">
                        <!-- @if ($errors->has('store_name'))
                        <span class="help-block" id="errorMessageStorename" >
                            <span style="color: red;" id="errorMsgStorename" class='validate'>{{ $errors->first('store_name') }}</span>
                        </span> -->
                        <!-- @endif -->
                        <span class="help-block" id="errorMessageStorename1" style="display: none;">
                            <span style="color: red;display: none;" id="errorMsgStorename" class='validate'></span>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.name_of_store') !!} [FR] <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="store_name_fr" id="store_name" class="form-control" placeholder="Name of store [FR]" value="{{ old('store_name_fr') }}">
                        <!-- @if ($errors->has('store_name_fr'))
                        <span class="help-block">
                            <span style="color: red;" id="errorMessageStorenamefr" class='validate'>{{ $errors->first('store_name_fr') }}</span>
                        </span>
                        @endif -->
                        <span class="help-block" id="errorMessageStorenamefr1" style="display:none">
                            <span style="color: red;display: none;" id="errorMsgStorenamefr" class='validate'></span>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.store_contact_number') !!} <span class="valid_field">*</span></label>
                    <div class="col-sm-2">
                        <select style="font-size:15px !important;" name="phone_code" id="phone_code" class="form-control">
                            @foreach ($countryData as $value)
                            <option value="{{$value->phonecode}}">+{{$value->phonecode.' ('.$value->shortname.')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="{!! __('backend.store_contact_number') !!}" value="">
                        <span class="help-block" id="errorMessageContactNumber" style="display:none">
                            <span style="color: red;display: none;" id="errorMsgContactNumber" class='validate'></span>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.address') !!} <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="{{old('address')}}">
                        <!-- @if ($errors->has('address'))
                        <span class="help-block">
                            <span style="color: red;" id="errorMessageAddress" class='validate'>{{ $errors->first('address') }}</span>
                        </span>
                        @endif -->
                        <span class="help-block" id="errorMessageAddress1" style="display:none">
                            <span style="color: red;display: none;" id="errorMsgAddress" class='validate'></span>
                        </span>
                        <input type="hidden" name="property_latitude" id="property_latitude" value="{{old('property_latitude')}}">
                        <input type="hidden" name="property_longitude" id="property_longitude" value="{{old('property_longitude')}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 "></label>
                    <div class="col-sm-9">
                        <div id="myMapaaa"></div>
                    </div>
                </div>
                <div class="form-group row weekplanid" id="border" style="border: 1px solid; border-radius: 21px;">
                    <br>
                    <label class="col-sm-2 form-control-label" id="pastekey">{!! __('backend.bussiness_hours') !!}</label>
                    <div class="col-sm-10">
                        <div class="multi-group">
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">Monday</label>
                                <div class="col-sm-4">
                                    <label>Opening Time</label>
                                    <input type="time" class="form-control time" name="monday_opening_time" id="monday_opening_time" value="{{ @old('monday_opening_time')?: '00:00'  }}">
                                </div>
                                <div class="col-sm-4">
                                    <label>Closing Time</label>
                                    <input type="time" class="form-control" name="monday_close_time" id="monday_close_time" value="{{ @old('monday_close_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-2">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">Tuesday</label>
                                <div class="col-sm-4">
                                    <label>Opening Time</label>
                                    <input type="time" class="form-control" name="tuesday_opening_time" id="tuesday_opening_time" value="{{ @old('tuesday_opening_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-4">
                                    <label>Closing Time</label>
                                    <input type="time" class="form-control" name="tuesday_close_time" id="tuesday_close_time" value="{{ @old('tuesday_close_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-2">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">Wednesday</label>
                                <div class="col-sm-4">
                                    <label>Opening Time</label>
                                    <input type="time" class="form-control" name="wednesday_opening_time" id="wednesday_opening_time" value="{{ @old('wednesday_opening_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-4">
                                    <label>Closing Time</label>
                                    <input type="time" class="form-control" name="wednesday_close_time" id="wednesday_close_time" value="{{ @old('wednesday_close_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-2">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">Thursday</label>
                                <div class="col-sm-4">
                                    <label>Opening Time</label>
                                    <input type="time" class="form-control" name="thursday_opening_time" id="thursday_opening_time" value="{{ @old('thursday_opening_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-4">
                                    <label>Closing Time</label>
                                    <input type="time" class="form-control" name="thursday_close_time" id="thursday_close_time" value="{{ @old('thursday_close_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-2">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">Friday</label>
                                <div class="col-sm-4">
                                    <label>Opening Time</label>
                                    <input type="time" class="form-control" name="friday_opening_time" id="friday_opening_time" value="{{ @old('friday_opening_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-4">
                                    <label>Closing Time</label>
                                    <input type="time" class="form-control" name="friday_close_time" id="friday_close_time" value="{{ @old('friday_close_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-2">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">Saturday</label>
                                <div class="col-sm-4">
                                    <label>Opening Time</label>
                                    <input type="time" class="form-control" name="saturday_opening_time" id="saturday_opening_time" value="{{ @old('saturday_opening_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-4">
                                    <label>Closing Time</label>
                                    <input type="time" class="form-control" name="saturday_close_time" id="saturday_close_time" value="{{ @old('saturday_close_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-2">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label">Sunday</label>
                                <div class="col-sm-4">
                                    <label>Opening Time</label>
                                    <input type="time" class="form-control" name="sunday_opening_time" id="sunday_opening_time" value="{{ @old('sunday_opening_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-4">
                                    <label>Closing Time</label>
                                    <input type="time" class="form-control" name="sunday_close_time" id="sunday_close_time" value="{{ @old('sunday_close_time')?: '00:00' }}">
                                </div>
                                <div class="col-sm-2">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row m-t-md">
                    <div class="col-sm-10 pl-0">
                        <button type="submit" class="btn btn-primary m-t" id="submitDetail"><i class="material-icons">&#xe31b;</i> {!! __('backend.add') !!}</button>
                        <a href="{{ route('store') }}" class="btn btn-default m-t">
                            <i class="material-icons">
                                &#xe5cd;</i> {!! __('backend.cancel') !!}
                        </a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
    @push('after-scripts')
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY', 'AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4') }}&libraries=places&callback=initialize1" async defer></script>
-->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4') }}&libraries=places&callback=initialize1" async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.14.0/jquery.timepicker.min.js" integrity="sha512-s0SB4i9ezk9SRyV1Glrj/w5xS5ExSxXiN44fQeV9GYOtExbVWnC+mUsUyZdIYv6qXL0xe1qvpe0h1kk56gsgaA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
        function isNumberKey(evt) {
            //var e = evt || window.event;
            var keyCode = (evt.which) ? evt.which : evt.keyCode;
            if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

                return false;
            return true;

        }
        $(document).ready(function() {
            showmapView();
        })
        $(document).ready(function() {
            $('#storeForm').submit(function(event) {
                event.preventDefault(); // Prevent the default form submission
                // Perform the AJAX request
                submitForm();
            });
        });

        function submitForm() {
            // Serialize the form data
            const formData = $('#storeForm').serialize();
            var url = "{{route('store.store')}}";
            var csrf = "{{ csrf_token() }}";
            // Make an AJAX request to your Laravel route
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                success: function(response) {
                    // Handle the response (if any)
                    // if (response.success) {
                                $('.loader').css("visibility", "visible");
                                window.location.href = "{{ route('store')}}";
                            // }
                },
                // error: function(error) {
                //     // console.error('Error:', error);
                //     var errorData = JSON.parse(error.responseText);
                //     // console.error('Error:', error);
                //         console.log(error.errorData); 
                //     // console.log(error.store_name_fr);
                //     // Check if store_name and store_name_fr fields are defined in the error JSON
                //     if (errorData.store_name) {
                //         $("span#errorMessageStorename").css("display", "block");
                //         $("span#errorMessageStorenamefr").css("display", "block");
                //         $("span#errorMsgStorename").html(errorData.store_name[0]);
                //     }
                //     if (errorData.store_name_fr) {
                //         $("span#errorMessageStorename").css("display", "block");
                //         $("span#errorMessageStorenamefr").css("display", "block");
                //         $("span#errorMsgStorenamefr").html(errorData.store_name_fr[0]);
                //     }
                //     // Add similar checks for other fields

                //     // Display validation errors
                //     if (errorData.address) {
                //         $("span#errorMessageAddress").css("display", "block");
                //         $("span#errorMsgAddress").html(errorData.address[0]);
                //     }
                // }
                error: function(errors) {
                            // alert(errors);
                            // $('.loader').css("visibility", "none");
                            var erroJson = JSON.parse(errors.responseText);
                           // console.log(erroJson);
                            for (var err in erroJson) {
                                for (var errstr of erroJson[err])
                                    //console.log(err);

                                    if (err == "store_name") {
                                       // alert(1);
                                      
                                        $("#errorMessageStorename1").css("display", "block");
                                        $("#errorMsgStorename").css("display", "block");
                                        $("#errorMsgStorename").html(errstr);
                                    }
                                    else if(err == "store_name_fr") {
                                        // $("span#errorMessageAddress1").css("display", "none");
                                        // $("span#errorMessageStorename1").css("display", "none");
                                        $("span#errorMessageStorenamefr1").css("display", "block");
                                        $("span#errorMsgStorenamefr").css("display", "block");
                                        $("span#errorMsgStorenamefr").html(errstr);
                                    }
                                    else if(err == "contact_number") {
                                        // $("span#errorMessageAddress1").css("display", "none");
                                        // $("span#errorMessageStorename1").css("display", "none");
                                        $("span#errorMessageStorenamefr1").css("display", "block");
                                        $("span#errorMsgStorenamefr").css("display", "block");
                                        $("span#errorMsgStorenamefr").html(errstr);
                                    }
                                    else{
                                    //     $("span#errorMessageStorenamefr1").css("display", "none");
                                    //     $("span#errorMessageStorename1").css("display", "none");
                                       $("span#errorMessageContactNumber").css("display", "block");
                                       $("span#errorMsgContactNumber").css("display", "block");
                                        $("span#errorMsgContactNumber").html(errstr);
                                    }
                            }
                        }
              
            });
        }

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
            var address = $("#address").val();
            // alert(address)
            if (address) {
                // loader_show();
                initialize(latitude, longitude, flag);

            } else {
                $('#myMapaaa').parents('.col-md-12').hide();
            }
        }


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
                // console.log(place.id)
                // return false;
                // getArea(latitude,longitude);
                showmapView(latitude, longitude, 0);
                // get_area(latitude, longitude);
                $("#property_latitude").val(latitude);
                $("#property_longitude").val(longitude);
                console.log(place.address_components);
            });
        }

        function initialize(lat, long, flag) {
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
            if (!isNaN(lat) && !isNaN(long)) {
                var myLatlng = new google.maps.LatLng(parseFloat(lat), parseFloat(long));
                // Rest of your code to set up the map and marker
            }
            // var myLatlng = new google.maps.LatLng(lat, long);

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

            // function xyz(latitude, longitude) {
            //     // alert('hello')
            //     // loader_show();
            //     $.ajax({

            //         url: "{{route('getAddress')}}",
            //         type: 'post',
            //         data: 'latitude=' + latitude + '&longitude=' + longitude,
            //         success: function(data) {
            //             $("#address").val(data);
            //             // loader_hide();
            //             initialize(latitude, longitude);
            //         }
            //     });
            // }
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