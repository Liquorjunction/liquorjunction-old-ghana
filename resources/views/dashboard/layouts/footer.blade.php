<div class="app-footer">
  <div class="p-a text-xs">
    <div class="pull-right text-muted">
      &copy;<?php echo date("Y") ?> Copyright
      <strong>liquor</strong> &
      Crafted by Vrinsoft.com
      <a ui-scroll-to="content" onclick="topFunction()" id="myBtn"><i class="fa fa-long-arrow-up p-x-sm"></i></a>
    </div>

    <div class="nav">
      &nbsp;
    </div>
  </div>
</div>
<script>
  //Get the button
  var mybutton = document.getElementById("myBtn");

  // When the user scrolls down 20px from the top of the document, show the button
  window.onscroll = function() {
    scrollFunction()
  };

  window.onload = function () {
    mybutton.style.display = "none";
  }

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      mybutton.style.display = "block";
    } else {
      mybutton.style.display = "none";
    }
  }

  // When the user clicks on the button, scroll to the top of the document
  function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  }
  
</script>
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