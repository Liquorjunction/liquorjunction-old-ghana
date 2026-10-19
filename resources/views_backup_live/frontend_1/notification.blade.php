
    <ul class="notification-block mb-0">                
        @foreach($notificationList as $notification)
        <?php 
    
        $date_time = \Helper::converttimeTozone($notification->created_at);

        $date_time = date('h:i A d M, Y',strtotime($date_time));

        if ($notification->notification_type == 1) {
            $icon = "fa-solid fa-check text-dark-green";
        }elseif ($notification->notification_type == 2) {
            $icon = "fa-solid fa-image text-orange";
            
        }elseif ($notification->notification_type == 3) {
            $icon = "fa-solid fa-van-shuttle text-pink";
            
        }elseif ($notification->notification_type == 4) {
            $icon = "fa-solid fa-check text-dark-green";
            
        }elseif ($notification->notification_type == 5) {
            $icon = "fa-solid fa-xmark text-dark-green";
            
        }else{
            $icon = "fa-solid fa-image text-orange";

        }
        ?>
        <li>
            <div class="preview-img">
                <span class="bg-light-orange"><i class="{{@$icon}}"></i></span>
            </div>                    
            <div class="preview-info mb-3">
                <span class="text-grey d-block">{{@$date_time}}</span>
                <p class="body-normal text-black"><a href="{{route('order-detail',['id'=>Helper::encodeUrl($notification->orderId)])}}">{{@$notification->order_id}} </a> {{@$notification->message}}</p>
            </div>
        </li>
        @endforeach
        
    </ul>

<script type="text/javascript">
    $('.btn-close').click(function(e) {
        $(".offcanvasNotification").removeClass('show');
        // window.location.reload();
    });
</script>