<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
       $("#langchange").on('change', function(){
            var language = $(this).val();
            let url = "{{route('frontend.changeLanguage')}}";
            $.ajax({
                url: url,
                type: 'post',
                data: {
                    language: language
                },
                success: function(result) {
                    location.reload();
                }
            });
       });
    });
</script>
<!-- Jquery JS -->
<script type="text/javascript" src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
    <!-- End Jquery JS -->
    <!-- Bootstrap JS -->
    <script type="text/javascript" src="{{ asset('assets/frontend/js/bootstrap.min.js') }}"></script>
    <!-- End Bootstrap JS -->
    <!-- Xzoom -->
    <script type="text/javascript" src="{{ asset('assets/frontend/js/xzoom.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/xzoom-slider-setup.js') }}"></script>
    <!-- Xzoom End -->
    <!-- Xzoom New -->
    <!-- <script type="text/javascript" src="{{ asset('assets/frontend/js/zoom-image.js') }}"></script> -->
    <!-- Xzoom New End -->
    <!-- Swiper Slider JS -->
    <script type="text/javascript" src="{{ asset('assets/frontend/js/swiper-bundle.min.js') }}"></script>
    <!-- End Swiper Slider JS -->
    <!-- Menu -->
    <script type="text/javascript" src="{{ asset('assets/frontend/js/menu.js') }}"></script>
    <!-- End Menu -->
    <!-- Custom JS -->
     <script type="text/javascript" src="{{ asset('assets/frontend/js/custom.js') }}"></script> 
    <!-- End Custom JS -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/npm-sweetalert.js') }}"></script>

    <script src="{{ asset('assets/frontend/js/moment.js/2.19.0/moment.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/moment-timezone/0.5.13/moment-timezone-with-data.js') }}"></script>
    <script>
        $(document).ready(function(){
            var timezone = moment.tz.guess();
            var language = $(this).val();
            let url = "{{route('frontend.setTimeZone')}}";
            $.ajax({
                url: url,
                type: 'post',
                data: {
                    timezone: timezone
                },
                success: function(result) {
                   
                }
            });        
        });
        
    </script>