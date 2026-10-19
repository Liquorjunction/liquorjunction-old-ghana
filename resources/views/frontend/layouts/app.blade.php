@include('frontend.layouts.head')
@include('frontend.layouts.header')
@yield('content')
@include('frontend.layouts.footer')
@include('frontend.layouts.foot')
<style>
/* Shake animation for Add to Cart button */
.shake {
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}
@keyframes shake {
  10%, 90% { transform: translateX(-2px); }
  20%, 80% { transform: translateX(4px); }
  30%, 50%, 70% { transform: translateX(-8px); }
  40%, 60% { transform: translateX(8px); }
}
</style>
@stack('after-scripts')

    
    <!-- begin olark code -->
    {{-- <script type="text/javascript" async> ;(function(o,l,a,r,k,y){if(o.olark)return; r="script";y=l.createElement(r);r=l.getElementsByTagName(r)[0]; y.async=1;y.src="//"+a;r.parentNode.insertBefore(y,r); y=o.olark=function(){k.s.push(arguments);k.t.push(+new Date)}; y.extend=function(i,j){y("extend",i,j)}; y.identify=function(i){y("identify",k.i=i)}; y.configure=function(i,j){y("configure",i,j);k.c[i]=j}; k=y._={s:[],t:[+new Date],c:{},l:a}; })(window,document,"static.olark.com/jsclient/loader.js");
/* custom configuration goes here (www.olark.com/documentation) */
olark.identify('2738-615-10-5940');</script> --}}

<!-- end olark code -->

<script>
    var isMinPriceLoad = true;
var isMaxPriceLoad = true;


window.onload = function () {
  // Check if the element with ID "slider-1" exists
  let sliderOne = document.getElementById("slider-1");
  let sliderTwo = document.getElementById("slider-2");
  if (sliderOne) {
    // If the element exists, load the JavaScript functions
    slideOne();
  }if(sliderTwo){
    slideTwo();
  }
};

let sliderOne = document.getElementById("slider-1");
let sliderTwo = document.getElementById("slider-2");
let displayValOne = document.getElementById("range1");
let displayValTwo = document.getElementById("range2");
let minGap = 0;
let sliderTrack = document.querySelector(".slider-track");
let sliderMaxValue = document.getElementById("slider-1").max;
if (currency_type == "") {
  currency_type = "";
}
function slideOne() {
  if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
    sliderOne.value = parseInt(sliderTwo.value) - minGap;
  }
  //This is for style z- index 
  if(sliderOne.value==parseInt(sliderTwo.value)){   
    $("#slider-2").css('z-index','0');
  }else{
    $("#slider-2").css('z-index','2');
  }
  console.log('sliderOne',sliderOne.value);
  console.log('sliderTwo',parseInt(sliderTwo.value));

  if (!isMinPriceLoad) {
    $("#min-price").val(sliderOne.value);
  }

  //$("#min-price").val(sliderOne.value);
  displayValOne.textContent = sliderOne.value + currency_type;
  fillColor();
  isMinPriceLoad = false;
}
function slideTwo() {
  if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
    sliderTwo.value = parseInt(sliderOne.value) + minGap;
  }
  
  if(sliderOne.value==parseInt(sliderTwo.value)){   
    $("#slider-2").css('z-index','2');
  }else{
    $("#slider-2").css('z-index','0');
  }
  if (!isMaxPriceLoad) {
    $("#max-price").val(sliderTwo.value);
  }
  displayValTwo.textContent = sliderTwo.value + currency_type;
  fillColor();
  isMaxPriceLoad = false;
}
function fillColor() {
  percent1 = (sliderOne.value / sliderMaxValue) * 100;
  percent2 = (sliderTwo.value / sliderMaxValue) * 100;
  sliderTrack.style.background = `linear-gradient(to right, #DDDDDD ${percent1}% , #FBB516 ${percent1}% , #FBB516 ${percent2}%, #DDDDDD ${percent2}%)`;
}

@php 

$data = \App\Models\Setting::first();
$contact = \App\Models\Country::where('status',1)->first();

// dd($contact);

@endphp


</script>

<script type="text/javascript">
  window._mfq = window._mfq || [];
  (function() {
    var mf = document.createElement("script");
    mf.type = "text/javascript"; mf.defer = true;
    mf.src = "//cdn.mouseflow.com/projects/a1f36707-b54f-44e6-974e-cd307ffbe8f8.js";
    document.getElementsByTagName("head")[0].appendChild(mf);
  })();
</script>

{{-- <a href="https://wa.me/233{{$data->phone}}" class="fixed-watsapp" target="_blank">  <svg class="icon-badge heartbeat" width="40" height="40" viewBox="0 0 20 20"> <image width="20" height="20" href="https://cliqtechno.vrinsoft.in/wp-content/themes/shoretech/images/whatsapp.svg"></image> </svg> --}}
  {{-- (+{{$contact->phonecode}}) {{$data->phone}}</a> --}}

<a href="https://wa.me/233{{$data->phone}}" class="fixed-watsapp" target="_blank">
  <img src="{{ asset('assets/frontend/images/whatsapp.svg') }}" alt="whatsapp" title="whatsapp" class="icon-badge heartbeat" width="40" height="40" />
</a>

</body>
</html>