@extends('frontEnd.layouts.new_app')
@section('title','Store Map')
@section('content')
@include('sweetalert::alert')

    <main class="site-content">
        <div class="bread-crumb-block">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="landing.php" class="text-grey body-normal">Home</a></li>
                    <li><a href="{{route('store.listing-online')}}" class="text-grey body-normal">Store</a></li>
                    <li><p class="text-black body-normal">Store Map</p></li>
                </ul>
            </div>
        </div>

        <div class="store-location pt-40 pb-80">
            <div class="container">
                <div class="row">
                    <h1 class="mb-0">Store Map</h1>
                    <div class="col-lg-6">
                        <div class="store-location-map">
                            <div id="map" style='height:700px'>
                                
                            </div>
                        <!-- <iframe id="map" src="" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> -->
                        <div class="store-detail-map" id="storeDetail"> 
                           
                        </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="store-location-list">
                            @if($storeMapData->count() > 0)
                            @foreach($storeMapData as $map)
                            <?php
                            $storeTiming = DB::table('store_timing_week')->leftjoin('week_list','week_list.id','=','store_timing_week.week_id')->where('store_timing_week.status',1)->where('store_timing_week.store_id',$map->id)->select('store_timing_week.*','week_list.name as week_name')->get();
                            // echo "<pre>";print_r($storeTiming);exit();
                            ?>
                            <li class="store-location-block">
                                <input type="hidden" name="store_id" id="store_id" value="{{@$map->id}}">
                                <input type="hidden" name="wholesaler_id" id="wholesaler_id" value="{{@$map->wholesaler_id}}">
                                <div class="store-info-block">
                                    <span class="d-block store-image">
                                        @php($storeImage = ($map->profile)?'uploads/customer/'.$map->profile:"assets/frontend/images/store_image.png")
                                        <img src="{{ asset($storeImage) }}" alt="store-logo" title="Store Logo" />
                                    </span>
                                    <h4 class="mb-0">{{@$map->store_name}}</h4>
                                </div>
                                <div class="store-timmings">
                                    <h6>Opening Timings</h6>
                                    <ul>
                                        @foreach($storeTiming as $timing)

                                        <li class="timmings">
                                            <p class="body-normal">{{@$timing->week_name}}</p>
                                            @if($timing->start_time == "00:00:00")
                                            <span class="body-normal red-text">Closed</span>
                                            @else
                                            <span class="body-normal">{{date('h:i A', strtotime($timing->start_time));}} -  {{date('h:i A', strtotime($timing->end_time));}}</span>
                                            @endif
                                        </li>
                                        @endforeach
                                        
                                    </ul>
                                </div>
                            </li>
                            @endforeach
                            @else
                            <h3>No Data Found</h3>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&callback=initMap" async defer></script>
    <script type="text/javascript">
        function initMap() {
            // alert()
        const locations = <?php echo json_encode($locations) ?>;
        // console.log(locations)
        // return false;
        // const map = new google.maps.Map(document.getElementById("map"));
        var myLatLng = {lat : <?php echo $current_lat; ?>,lng: <?php echo $current_long; ?>};
// alert(myLatLng);
var map = new google.maps.Map(document.getElementById('map'), {
  center: myLatLng,
  scrollwheel: false,
  zoom: 12
 });

var mIcon = {
    path: google.maps.SymbolPath.CIRCLE,
    fillOpacity: 1,
    fillColor: '#48A0DC',
    strokeOpacity: 1,
    strokeWeight: 1,
    strokeColor: '#333',
    scale: 12
  };

  const svgMarker = {
path: "M10.453 14.016l6.563-6.609-1.406-1.406-5.156 5.203-2.063-2.109-1.406 1.406zM12 2.016q2.906 0 4.945 2.039t2.039 4.945q0 1.453-0.727 3.328t-1.758 3.516-2.039 3.070-1.711 2.273l-0.75 0.797q-0.281-0.328-0.75-0.867t-1.688-2.156-2.133-3.141-1.664-3.445-0.75-3.375q0-2.906 2.039-4.945t4.945-2.039z",
fillColor: '#48A0DC',
fillOpacity: 0.6,
strokeWeight: 0,
rotation: 0,
scale: 2,
anchor: new google.maps.Point(15, 30),
};
  var myStyle = {
      hide: [
        {
          featureType: 'poi',
          stylers: [{visibility: 'off'}]
        }
      ]
    };
    map.setOptions({styles: myStyle['hide']});
        var infowindow = new google.maps.InfoWindow();
        var bounds = new google.maps.LatLngBounds();
        for (var location of locations) {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(location.lat, location.lng),
                map: map
            });
            bounds.extend(marker.position);
            google.maps.event.addListener(marker, 'click', (function(marker, location) {
                return function() {
                getdetails(location.id);
                    // infowindow.setContent(location.lat + " & " + location.lng);
                    // infowindow.open(map, marker);
                }
            })(marker, location));

        }
        map.fitBounds(bounds);
    }

    function getdetails(id)
    {
        // alert(id)
        $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('storemap-detail')}}",
                    data: {'id': id},
                    success: function(response){
                        // alert(data.success)
                        // alert('hello')
                        // return false;
                       $(document).find("#storeDetail").append(response.html);
                    }
                });
    }

    $(".store-location-block").click(function(e){
        e.preventDefault()
        var store_id = $(this).find("#wholesaler_id").val();
        // $(".loader").fadeIn();
        $('.loader').css("visibility", "visible");
        // alert(store_id)
        var url = "{{ route('productlistview', ['id' => ':store_id']) }}";
        url = url.replace(':store_id', store_id);

        window.location.href = url;
});
    </script>

@endsection