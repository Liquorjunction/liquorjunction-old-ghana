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
