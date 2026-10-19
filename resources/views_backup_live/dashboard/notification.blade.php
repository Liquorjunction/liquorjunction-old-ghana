<style>
    iframe {
  border: 0;
  width: 100%;
  border-radius: 0;
}
a {
  font-weight: 400;
  font-size: 16px;
  line-height: 19px;
  display: inline-block;
  color: #2b2b2b;
  text-decoration: none;
  transition: 0.4s;
}
a:focus {
  outline: none;
}
a:active,
a:hover {
  color: #fbb516;
  outline: 0;
  transition: 0.4s;
}
a:hover,
a:focus {
  opacity: 1;
}
ul {
  padding: 0;
  margin: 0;
  list-style: none;
}
i {
    display: flex;
}
.fa-check:before {
    content: "\f00c"; /* Unicode for check icon */
                font-family: "Font Awesome 5 Free";
                font-weight: 900;
                color: #28a745; /* Green color */
}
p:last-child {
    margin-bottom: 0;
}
p {
    font-size: 16px;
    line-height: 24px;
    font-weight: 400;
    color: #858584;
}
.notification-block {
        max-height: 600px; /* Adjust the height as needed */
        overflow-y: auto;
    }
.fa-image:before {
    content: "\f03e"; /* Unicode for image icon */
    font-family: "Font Awesome 5 Free"; /* Ensure the correct Font Awesome font family */
    font-weight: 900; /* Font Awesome 5 Free requires a font-weight for icons */
    color: #007bff; /* Blue color for the image icon */
}
    </style>

    <ul class="notification-block mb-0">                
        @foreach($notificationList as $notification)
        <?php 
    
        $date_time = \Helper::converttimeTozone($notification->created_at);

        $date_time = date('h:i A d M, Y',strtotime($date_time));

        if ($notification->notification_type == 1) {
            $icon = "fa-solid fa-check text-dark-green";
        }elseif ($notification->notification_type == 2) {
            $icon = "fa-solid fa-image text-orange";
            
        }else{
            $icon = "fa-solid fa-image text-orange";

        }
        ?>
        @if($notification->notification_type == 1)
            <!-- <li> -->
            <li style="background-color: {{ @$notification->is_read == 0 ? '#ffc23647' : 'transparent' }};">
   
        
                <div class="preview-img">
                    <span class="bg-light-orange"><i class="{{@$icon}}"></i></span>
                </div>                    
                <div class="preview-info mb-3">
                    <span class="text-grey d-block">{{@$date_time}}</span>
                    <p class="body-normal text-black"><a href="{{route('adminorder.show',[($notification->orderId)])}}">{{@$notification->order_id}} </a> {{@$notification->message}}</p>
                </div>
            </li>
        @else
        <!-- <li> -->
        <li style="background-color: {{ @$notification->is_read == 0 ? '#ffc23647' : 'transparent' }};">
   
        
                <div class="preview-img">
                    <span class="bg-light-orange"><i class="{{@$icon}}"></i></span>
                </div>                    
                <div class="preview-info mb-3">
                    <span class="text-grey d-block">{{@$date_time}}</span>
                    <p class="body-normal text-black"><a href="{{route('inquiry.show',[($notification->inquiryId)])}}">{{@$notification->inquiryId}} </a> {{@$notification->inquiry_message}}</p>
                </div>
            </li>

        @endif
        @endforeach
        
    </ul>

<script type="text/javascript">
    $('.btn-close').click(function(e) {
        $(".offcanvasNotification").removeClass('show');
        // window.location.reload();
    });
</script>