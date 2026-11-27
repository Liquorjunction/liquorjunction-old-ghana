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
                    <a class="nav-link" href="{{ route('store') }}">
                        <i class="material-icons md-18">×</i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-body">
            {{ Form::open(['route' => ['store.update', @$StoreDetails->id], 'method' => 'POST', 'files' => true, 'enctype' => 'multipart/form-data', 'id' => 'storeForm']) }}
            <div class="personal_informations">
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.name_of_store') !!}<span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="store_name" id="store_name" class="form-control" placeholder="Name of store" value="{{ @$StoreDetails->store_name }}">
                        @if ($errors->has('store_name'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('store_name') }}</span>
                        </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.store_contact_number') !!} <span class="valid_field">*</span></label>
                    <div class="col-sm-2">
                        <select style="font-size:15px !important;" name="phone_code" id="phone_code" class="form-control">
                            @foreach ($countryData as $value)
                            <option value="{{$value->phonecode}}"  {{ ($StoreDetails->phone_code == $value->phonecode) ? 'selected' : ''}}>+{{$value->phonecode}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-7">
                        <input type="text" name="contact_number" id="contact_number" maxlength="15" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" class="form-control" placeholder="{!! __('backend.store_contact_number') !!}"  value="{{ @$StoreDetails->contact_number}}">
                        @if ($errors->has('contact_number'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('contact_number') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">Store Email <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Store Email (e.g. sales@liquorjunction.co)" value="{{ old('email', @$StoreDetails->email) }}">
                        @if ($errors->has('email'))
                        <span class="help-block">
                            <span style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">{!! __('backend.address') !!} <span class="valid_field">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="address" id="address" class="form-control" placeholder="Search location in {{ $countryDisplayNames }} or enter full address" value="{{ old('address', @$StoreDetails->address) }}">
                        @if ($errors->has('address'))
                        <span class="help-block" id="serverErrorMessageAddress">
                            <span style="color: red;" class='validate'>{{ $errors->first('address') }}</span>
                        </span>
                        @endif
                        <span class="help-block" id="errorMessageAddress1" style="display:none">
                            <span style="color: red;display: none;" id="errorMsgAddress" class='validate'></span>
                        </span>
                        <input type="hidden" name="property_latitude" id="property_latitude" value="{{ old('property_latitude', @$StoreDetails->latitude) }}">
                        <input type="hidden" name="property_longitude" id="property_longitude" value="{{ old('property_longitude', @$StoreDetails->longitude) }}">
                        <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code') }}">
                        <input type="hidden" name="country" id="country_name" value="{{ old('country', @$StoreDetails->country) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 form-control-label">Location on Map</label>
                    <div class="col-sm-9">
                        <!-- Interactive Map Card with Center Pin -->
                        <div class="store-map-card" style="position: relative; width: 100%; height: 380px; border-radius: 12px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.06); background-color: #f8fafc;">
                            <!-- Floating Locate Me Button (GPS button) -->
                            <button type="button" class="btn btn-white shadow" id="btn_locate_me" 
                                    title="Locate Current Position" 
                                    style="position: absolute; right: 12px; top: 12px; z-index: 10; width: 38px; height: 38px; border-radius: 8px; padding: 0; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; background: #fff; cursor: pointer;">
                                <i class="fa fa-crosshairs text-primary" id="locate_icon" style="font-size: 18px;"></i>
                            </button>

                            <!-- Google Map Canvas -->
                            <div id="myMapaaa" style="width: 100%; height: 100%;"></div>

                            <!-- Fixed Center Pin Marker -->
                            <div class="map-center-pin" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -100%); pointer-events: none; z-index: 5; text-align: center;">
                                <div class="pin-icon" style="transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                                    <svg width="42" height="42" viewBox="0 0 24 24" fill="#dc2626" style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.4));">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                                <div class="pin-shadow" style="width: 12px; height: 5px; background: rgba(0,0,0,0.25); border-radius: 50%; margin: -4px auto 0; filter: blur(1px);"></div>
                            </div>
                        </div>

                        <!-- Bottom Status Bar Below Map -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 12px; color: #64748b;">
                            <span id="map_status_text">
                                <i class="fa fa-info-circle text-info"></i> Drag map to set pin or search location in {{ $countryDisplayNames }}
                            </span>
                            <span id="map_coords_badge" class="badge" style="background: #e2e8f0; color: #334155; font-family: monospace; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                                Lat: <span id="badge_lat">{{ old('property_latitude', @$StoreDetails->latitude ?: '5.6037') }}</span>, Lng: <span id="badge_lng">{{ old('property_longitude', @$StoreDetails->longitude ?: '-0.1870') }}</span>
                            </span>
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
    @push('after-scripts')
    <script type="text/javascript">
        // Store Map Logic (Interactive center pin & country serviceability matching admin/country)
        var storeMap, storeGeocoder, isMapDragging = false, isProgrammaticMovement = false;
        var isStoreLocationAllowed = true;
        var allowedCountryCodes = {!! json_encode(array_values(array_map('strtolower', $allowedCountryCodes))) !!};
        var allowedCountryNames = {!! json_encode(array_values($allowedCountryNames)) !!};
        var allowedNamesStr = {!! json_encode($countryDisplayNames) !!};

        $(document).ready(function() {
            $('#storeForm').on('submit', function(e) {
                if (!isStoreLocationAllowed) {
                    e.preventDefault();
                    var alertMsg = $('#errorMsgAddress').text() || ('Stores can only be added in: ' + allowedNamesStr + '.');
                    $('#errorMessageAddress1').show();
                    $('#errorMsgAddress').show().text(alertMsg);
                    $('html, body').animate({
                        scrollTop: $('#address').offset().top - 120
                    }, 300);
                    return false;
                }
            });
        });

        // Helper: Extract country code & name from Google geocoder / autocomplete address components across all results
        function extractCountryFromResults(results) {
            if (!results || !results.length) return null;
            for (var r = 0; r < results.length; r++) {
                var comps = results[r].address_components || [];
                for (var i = 0; i < comps.length; i++) {
                    var comp = comps[i];
                    if (comp.types && comp.types.indexOf('country') !== -1) {
                        return {
                            code: (comp.short_name || '').toUpperCase().trim(),
                            name: (comp.long_name || '').trim()
                        };
                    }
                }
            }
            return null;
        }

        // Robust country resolution (handles Google components, Plus Codes, Indian PIN codes, coordinates, and fallback)
        function resolveLocationCountry(results, lat, lng, fullAddress, onDone) {
            var countryInfo = extractCountryFromResults(results);
            if (countryInfo && countryInfo.code) {
                onDone(countryInfo, fullAddress);
                return;
            }

            // Check if any result has an Indian postal code (6 digits starting with 1-9)
            if (results && results.length) {
                for (var r = 0; r < results.length; r++) {
                    var comps = results[r].address_components || [];
                    for (var i = 0; i < comps.length; i++) {
                        if (comps[i].types && comps[i].types.indexOf('postal_code') !== -1) {
                            var pc = (comps[i].short_name || '').trim();
                            if (/^[1-9][0-9]{5}$/.test(pc)) {
                                onDone({ code: 'IN', name: 'India' }, fullAddress);
                                return;
                            }
                        }
                    }
                }
            }

            // Coordinate bounding box checks (India: ~8.0-37.5 N, ~68.0-97.5 E; Ghana: ~4.5-11.5 N, ~-3.5-1.5 E)
            if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                if (lat >= 8.0 && lat <= 37.5 && lng >= 68.0 && lng <= 97.5) {
                    onDone({ code: 'IN', name: 'India' }, fullAddress);
                    return;
                } else if (lat >= 4.5 && lat <= 11.5 && lng >= -3.5 && lng <= 1.5) {
                    onDone({ code: 'GH', name: 'Ghana' }, fullAddress);
                    return;
                }
            }

            // Fallback: Query Nominatim reverse geocode using lat, lng
            if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                $.ajax({
                    url: 'https://nominatim.openstreetmap.org/reverse',
                    type: 'GET',
                    data: {
                        format: 'json',
                        lat: lat,
                        lon: lng,
                        zoom: 10,
                        addressdetails: 1
                    },
                    dataType: 'json',
                    timeout: 4000,
                    success: function(nomData) {
                        if (nomData && nomData.address && nomData.address.country_code) {
                            var nomCountry = {
                                code: (nomData.address.country_code || '').toUpperCase().trim(),
                                name: (nomData.address.country || '').trim()
                            };
                            onDone(nomCountry, nomData.display_name || fullAddress);
                        } else {
                            onDone(null, fullAddress);
                        }
                    },
                    error: function() {
                        onDone(null, fullAddress);
                    }
                });
                return;
            }

            onDone(null, fullAddress);
        }

        // Check country serviceability against countries configured in admin/country
        function handleStoreServiceability(countryInfo, fullAddress) {
            if (!countryInfo || !countryInfo.code) {
                isStoreLocationAllowed = false;
                $('#country_code').val('');
                $('#country_name').val('');
                var errorMsg = 'Could not verify country for this location. Stores can only be added in: ' + allowedNamesStr + '.';
                $('#errorMessageAddress1').show();
                $('#errorMsgAddress').show().text(errorMsg);
                $('#map_status_text').html('<span class="text-muted"><i class="fa fa-info-circle"></i> Drag map to set pin or search location in ' + allowedNamesStr + '</span>');
                return;
            }

            var codeLower = countryInfo.code.toLowerCase();
            var isAllowed = (allowedCountryCodes.indexOf(codeLower) !== -1);

            if (isAllowed) {
                isStoreLocationAllowed = true;
                $('#country_code').val(countryInfo.code);
                $('#country_name').val(countryInfo.name);
                $('#errorMessageAddress1').hide();
                $('#errorMsgAddress').hide().empty();
                $('#serverErrorMessageAddress').hide();
                $('#map_status_text').html('<span class="text-success"><i class="fa fa-check-circle"></i> Serviceable Country: ' + (countryInfo.name || countryInfo.code) + '</span>');
            } else {
                isStoreLocationAllowed = false;
                $('#country_code').val(countryInfo.code);
                $('#country_name').val(countryInfo.name);
                var blockedCountryName = countryInfo.name || countryInfo.code;
                var errorMsg = 'Store cannot be added in ' + blockedCountryName + '. Stores can only be added in: ' + allowedNamesStr + '.';
                
                // Show single error under Address field
                $('#errorMessageAddress1').show();
                $('#errorMsgAddress').show().text(errorMsg);
                $('#map_status_text').html('<span class="text-muted"><i class="fa fa-info-circle"></i> Drag map to set pin or search location in ' + allowedNamesStr + '</span>');
            }
        }

        function initStoreMap() {
            var rawLat = parseFloat($('#property_latitude').val());
            var rawLng = parseFloat($('#property_longitude').val());
            
            // Default center based on allowed countries (Ghana: 5.6037, -0.1870; India: 20.5937, 78.9629)
            var defaultLat = (allowedCountryCodes.indexOf('gh') !== -1) ? 5.603714 : ((allowedCountryCodes.indexOf('in') !== -1) ? 20.5937 : 5.603714);
            var defaultLng = (allowedCountryCodes.indexOf('gh') !== -1) ? -0.186964 : ((allowedCountryCodes.indexOf('in') !== -1) ? 78.9629 : -0.186964);

            var initialLat = (!isNaN(rawLat) && rawLat !== 0) ? rawLat : defaultLat;
            var initialLng = (!isNaN(rawLng) && rawLng !== 0) ? rawLng : defaultLng;
            var initialLatLng = new google.maps.LatLng(initialLat, initialLng);

            var mapOptions = {
                zoom: (!isNaN(rawLat) && rawLat !== 0) ? 16 : 14,
                center: initialLatLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                }
            };

            storeMap = new google.maps.Map(document.getElementById('myMapaaa'), mapOptions);
            storeGeocoder = new google.maps.Geocoder();

            // Map drag start: lift the center pin up
            google.maps.event.addListener(storeMap, 'dragstart', function() {
                isMapDragging = true;
                $('.map-center-pin .pin-icon').css('transform', 'translateY(-12px) scale(1.08)');
                $('#map_status_text').html('<i class="fa fa-arrows text-warning"></i> Moving map to set pin...');
            });

            // Map idle (after drag or pan): drop pin and reverse geocode center location
            google.maps.event.addListener(storeMap, 'idle', function() {
                $('.map-center-pin .pin-icon').css('transform', 'translateY(0) scale(1)');
                
                var center = storeMap.getCenter();
                var cLat = center.lat();
                var cLng = center.lng();

                $('#property_latitude').val(cLat.toFixed(7));
                $('#property_longitude').val(cLng.toFixed(7));
                $('#badge_lat').text(cLat.toFixed(4));
                $('#badge_lng').text(cLng.toFixed(4));

                if (isProgrammaticMovement) {
                    isProgrammaticMovement = false;
                    return;
                }

                if (isMapDragging) {
                    isMapDragging = false;
                    $('#map_status_text').html('<i class="fa fa-spinner fa-spin text-warning"></i> Checking country serviceability...');
                    storeGeocoder.geocode({ location: center }, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK && results && results.length) {
                            var bestAddress = results[0].formatted_address || '';
                            if (/^[A-Z0-9]{4,8}\+[A-Z0-9]{2,4}$/.test(bestAddress.trim()) && results[1] && results[1].formatted_address) {
                                bestAddress = bestAddress + ', ' + results[1].formatted_address;
                            }
                            $('#address').val(bestAddress);

                            resolveLocationCountry(results, cLat, cLng, bestAddress, function(countryInfo, resolvedAddress) {
                                handleStoreServiceability(countryInfo, $('#address').val());
                            });
                        } else {
                            resolveLocationCountry([], cLat, cLng, '', function(countryInfo, resolvedAddress) {
                                handleStoreServiceability(countryInfo, resolvedAddress);
                            });
                        }
                    });
                }
            });

            // Map click: pan smoothly to clicked position
            google.maps.event.addListener(storeMap, 'click', function(e) {
                if (!e.latLng) return;
                isMapDragging = true;
                storeMap.panTo(e.latLng);
                if (storeMap.getZoom() < 15) {
                    storeMap.setZoom(15);
                }
            });

            // Setup Autocomplete on the main Address input field
            var addrInput = document.getElementById('address');
            if (addrInput) {
                var addrAutocomplete = new google.maps.places.Autocomplete(addrInput);
                addrAutocomplete.bindTo('bounds', storeMap);

                google.maps.event.addListener(addrAutocomplete, 'place_changed', function() {
                    var place = addrAutocomplete.getPlace();
                    if (!place || !place.geometry) return;

                    var pLat = place.geometry.location.lat();
                    var pLng = place.geometry.location.lng();

                    isProgrammaticMovement = true;
                    storeMap.panTo(place.geometry.location);
                    storeMap.setZoom(17);

                    $('#property_latitude').val(pLat.toFixed(7));
                    $('#property_longitude').val(pLng.toFixed(7));
                    $('#badge_lat').text(pLat.toFixed(4));
                    $('#badge_lng').text(pLng.toFixed(4));

                    $('#map_status_text').html('<i class="fa fa-spinner fa-spin text-warning"></i> Checking country serviceability...');

                    resolveLocationCountry([place], pLat, pLng, place.formatted_address || $('#address').val(), function(countryInfo, resolvedAddress) {
                        handleStoreServiceability(countryInfo, resolvedAddress);
                    });
                });

                // Prevent form submit on Enter in address field and geocode
                $('#address').on('keydown', function(e) {
                    if (e.keyCode === 13) {
                        e.preventDefault();
                        var query = $(this).val();
                        if (query && storeGeocoder) {
                            $('#map_status_text').html('<i class="fa fa-spinner fa-spin text-warning"></i> Locating address on map...');
                            storeGeocoder.geocode({ address: query }, function(results, status) {
                                if (status === google.maps.GeocoderStatus.OK && results && results.length) {
                                    var loc = results[0].geometry.location;
                                    var rLat = loc.lat();
                                    var rLng = loc.lng();
                                    isProgrammaticMovement = true;
                                    storeMap.panTo(loc);
                                    storeMap.setZoom(17);
                                    $('#property_latitude').val(rLat.toFixed(7));
                                    $('#property_longitude').val(rLng.toFixed(7));
                                    $('#badge_lat').text(rLat.toFixed(4));
                                    $('#badge_lng').text(rLng.toFixed(4));
                                    if (results[0].formatted_address) {
                                        $('#address').val(results[0].formatted_address);
                                    }

                                    resolveLocationCountry(results, rLat, rLng, results[0].formatted_address || query, function(countryInfo, resolvedAddress) {
                                        handleStoreServiceability(countryInfo, resolvedAddress);
                                    });
                                } else {
                                    $('#map_status_text').html('<span class="text-muted"><i class="fa fa-info-circle"></i> Drag map to set pin or search location in ' + allowedNamesStr + '</span>');
                                    $('#errorMessageAddress1').show();
                                    $('#errorMsgAddress').show().text('Location not found. Please try another search.');
                                }
                            });
                        }
                        return false;
                    }
                });

                // Fallback: If address is entered manually and field loses focus, geocode it
                $('#address').on('blur', function() {
                    var query = $(this).val().trim();
                    if (query && storeGeocoder) {
                        storeGeocoder.geocode({ address: query }, function(results, status) {
                            if (status === google.maps.GeocoderStatus.OK && results && results.length) {
                                var loc = results[0].geometry.location;
                                resolveLocationCountry(results, loc.lat(), loc.lng(), results[0].formatted_address || query, function(countryInfo, resolvedAddress) {
                                    handleStoreServiceability(countryInfo, resolvedAddress);
                                });
                            }
                        });
                    }
                });
            }

            // Locate Me Button (GPS)
            $('#btn_locate_me').on('click', function() {
                if (!navigator.geolocation) {
                    alert('Geolocation is not supported by your browser.');
                    return;
                }

                $('#locate_icon').removeClass('fa-crosshairs').addClass('fa-spinner fa-spin text-warning');
                $('#map_status_text').html('<i class="fa fa-spinner fa-spin text-warning"></i> Detecting current location...');

                navigator.geolocation.getCurrentPosition(function(pos) {
                    $('#locate_icon').removeClass('fa-spinner fa-spin text-warning').addClass('fa-crosshairs');
                    var curLat = pos.coords.latitude;
                    var curLng = pos.coords.longitude;
                    var curLatLng = new google.maps.LatLng(curLat, curLng);

                    isMapDragging = true;
                    storeMap.panTo(curLatLng);
                    storeMap.setZoom(17);
                }, function(err) {
                    $('#locate_icon').removeClass('fa-spinner fa-spin text-warning').addClass('fa-crosshairs');
                    $('#map_status_text').html('<i class="fa fa-exclamation-triangle text-danger"></i> Location access denied or unavailable');
                }, { enableHighAccuracy: true, timeout: 8000 });
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'AIzaSyBwrXHtZoJ6y048ZwDBPrzgoKH0nmHHJLI') }}&libraries=places&callback=initStoreMap" async defer></script>
    @endpush