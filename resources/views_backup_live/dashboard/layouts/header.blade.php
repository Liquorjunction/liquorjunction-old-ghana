
<?php
// Fetch notifications count without user ID
$notificationListCount = DB::table('admin_notifications')->where('is_read', 0)->count();
$notificationList = DB::table('admin_notifications')->orderby('id', 'DESC')->limit(10)->get();
?>
<div class="app-header white box-shadow navbar-md">
    <div class="navbar">
        <!-- Open side - Naviation on mobile -->
        <a data-toggle="modal" data-target="#aside" class="navbar-item pull-left hidden-lg-up">
            <i class="material-icons">&#xe5d2;</i>
        </a>

        <div class="navbar-item pull-left h5" ng-bind="$state.current.data.title" id="pageTitle"></div>

        <!-- navbar right -->
        <ul class="nav navbar-nav pull-right">
            {{--<li class="nav-item p-t p-b">
                <a class="btn btn-sm info marginTop2" href="{{ route('HomePage') }}" target="_blank"
                   title="{{ __('backend.sitePreview') }}">
                    <i class="material-icons">&#xe895;</i> {{ __('backend.sitePreview') }}
                </a>
            </li>--}}
            <?php
           // $alerts = count(Helper::webmailsAlerts()) + count(Helper::eventsAlerts());
            ?>
            {{-- @if($alerts >0)
                <li class="nav-item dropdown pos-stc-xs">
                    <a class="nav-link" href data-toggle="dropdown">
                        <i class="material-icons">&#xe7f5;</i>
                        @if($alerts >0)
                            <span class="label label-sm up warn">{{ $alerts }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu pull-right w-xl animated fadeInUp no-bg no-border no-shadow">
                        <div class="box dark">
                            <div class="box p-a scrollable maxHeight320">
                               
                            </div>
                        </div>
                    </div>
                </li>
            @endif --}}


            
     <div class="offcanvas offcanvas-end offcanvasNotification" tabindex="-1" id="notificationoffcanvas" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h4 class="offcanvas-title" id="offcanvasRightLabel">{{ @Helper::language('notification') }}</h4>
            {{-- <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i
                    class="fa-solid fa-xmark"></i></button> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="offcanvas-body">
            
        </div>
    </div>

            
            <!-- <li class="header-cart" style="top:3px; margin-right:20px;">
                                <a href="javascript::void(0);" class="backdrop" title="Notification" onclick="return readNotification()" data-bs-toggle="offcanvas" data-bs-target="#notificationOffcanvas" aria-controls="offcanvasRight">
                                    <img src="{{ asset('assets/frontend/images/notification-bell.svg')}}" />
                                </a>
                               
                                @if($notificationListCount > 0)
                                <span class="count-number  notification-no">{{@$notificationListCount}}</span>
                                @endif
                                
            </li> -->

            <li class="header-cart" style="top:3px; margin-right:20px;">
    <a href="javascript::void(0);" class="backdrop" title="Notification" onclick="return readNotification()" data-bs-toggle="offcanvas" data-bs-target="#notificationOffcanvas" aria-controls="offcanvasRight">
        <img src="{{ asset('assets/frontend/images/notification-bell.svg')}}" />
    </a>
    @if($notificationListCount > 0)
    <span class="count-number  notification-no">{{ @$notificationListCount }}</span>
    @endif
</li>




            <li class="nav-item dropdown">
                <a class="nav-link clear" href data-toggle="dropdown">
                  <span class="avatar">
                      @if(Auth::user()->photo !="")
                          @if(Auth::user()->user_type == 1)
                          <img src="{{ asset('uploads/users/'.Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" style="vertical-align: middle; width:45px; height:38px; border-radius: 50%;border: 1px solid #2C2C2C;" 
                               title="{{ Auth::user()->name }}">
                          @else
                          <img src="{{ asset('uploads/customer/'.Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" style="vertical-align: middle; width:45px; height:38px; border-radius: 50%;border: 1px solid #2C2C2C;" 
                               title="{{ Auth::user()->name }}">
                          @endif
                      @else
                          <img src="{{ asset('uploads/contacts/profile.jpg') }}" alt="{{ Auth::user()->name }}" style="vertical-align: middle; width:45px; height:38px; border-radius: 50%;border: 1px solid #2C2C2C;"
                               title="{{ Auth::user()->name }}">
                      @endif
                      <!-- <i class="on b-white bottom"></i> -->
                  </span>
                </a>
                <div class="dropdown-menu pull-right dropdown-menu-scale">
                   
                   
                    <a class="dropdown-item" href="{{ route('usersEdit',Auth::user()->id) }}"><span>{{ __('backend.profile') }}</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin-change-password') }}"><span>Change Password</span></a>
                    <div class="dropdown-divider"></div>
                    <a id="logout" class="dropdown-item" href="{{ url('/logout') }}">Logout</a>

                    <form id="logout-form" action="{{ route('main-user-logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>

            <li class="nav-item hidden-md-up">
                <a class="nav-link" data-toggle="collapse" data-target="#collapse">
                    <i class="material-icons">&#xe5d4;</i>
                </a>
            </li>
            
        </ul>
        
  
        <!-- navbar collapse -->
        <div class="collapse navbar-toggleable-sm" id="collapse">
           
        <!-- link and dropdown -->
            
            <!-- / -->
        </div>
        <!-- / navbar collapse -->
    </div>

    
</div>

<style>
.i {
    display: flex;
}
.header-cart {
  position: relative;
}
.header-cart span.count-number.notification-no {
    top: -10px;
    right: -10px;
    border: 0;
}
.header-cart span {
    position: absolute;
    top: 0;
    right: 1px;
    color: #212121;
    width: 20px;
    height: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 13px;
    font-weight: bold;
    line-height: normal;
    letter-spacing: 0.55px;
    pointer-events: none;
}
.offcanvas {
    position: fixed;
    bottom: 0;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    max-width: 100%;
    visibility: hidden;
    background-color: #fff;
    background-clip: padding-box;
    outline: 0;
    transition: transform .3s ease-in-out
}

@media (prefers-reduced-motion:reduce) {
    .offcanvas {
        transition: none
    }
}

.offcanvas-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1rem
}

.offcanvas-header .btn-close {
    padding: .5rem .5rem;
    margin-top: -.5rem;
    margin-right: -.5rem;
    margin-bottom: -.5rem
}

.offcanvas-title {
    margin-bottom: 0;
    line-height: 1.5
}

.offcanvas-body {
    flex-grow: 1;
    padding: 1rem 1rem;
    overflow-y: auto;
}

.offcanvas-start {
    top: 0;
    left: 0;
    width: 400px;
    border-right: 1px solid rgba(0,0,0,.2);
    transform: translateX(-100%)
}

.offcanvas-end {
    top: 0;
    right: 0;
    width: 400px;
    border-left: 1px solid rgba(0,0,0,.2);
    transform: translateX(100%)
}

.offcanvas-top {
    top: 0;
    right: 0;
    left: 0;
    height: 30vh;
    max-height: 100%;
    border-bottom: 1px solid rgba(0,0,0,.2);
    transform: translateY(-100%)
}

.offcanvas-bottom {
    right: 0;
    left: 0;
    height: 30vh;
    max-height: 100%;
    border-top: 1px solid rgba(0,0,0,.2);
    transform: translateY(100%)
}

.offcanvas.show {
    transform: none
}
.offcanvas-header .btn-close {
    padding: .5rem .5rem;
    margin-top: -.5rem;
    margin-right: -.5rem;
    margin-bottom: -.5rem;
}
.btn-close {
    box-sizing: content-box;
    width: 1em;
    height: 1em;
    padding: .25em .25em;
    color: #000;
    background: transparent url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e) center / 1em auto no-repeat;
    border: 0;
    border-radius: .25rem;
    opacity: .5;
}
.filteroffcanvas .offcanvas-header .btn-close {
  position: absolute;
  top: 22px;
  left: 24px;
  padding: 0;
  color: #858584;
}
h4, .heading-four {
    font-size: 24px;
    line-height: 30px;
    font-weight: 600;
}
:root {
  scroll-behavior: unset;
}
* {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
*,
::before,
::after {
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
}
    </style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
     $(document).on("click", "#logout", function(e) {
        // alert('hello')
            e.preventDefault();
            var link = $(this).attr("href");
            // alert(link)
            // return false;
            Swal.fire({
  title: 'Logout ?',
  text: "Are you sure you want to logout ?",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: "#fbb516",
  cancelButtonColor: "rgb(36, 36, 36)",
  confirmButtonText: 'Yes',
  cancelButtonText:'No',
}).then((result) => {
  if (result.isConfirmed) {
    $('#logout-form').submit();
  }
})
        });


        $('.notification_btn').click(function(e) {
            // Add and Remove class from body 
            $('body').addClass('scrollidisable');
            e.stopPropagation();
        });

//         function readNotification() {

//            // console.log("notification");

//             $(".offcanvasNotification").addClass('show');
//         $(".offcanvasNotification").css('visibility', 'visible');

//             var data = 1;
//             action_url = "{{ route('read-admin-notification') }}";
//             var csrf = "{{ csrf_token() }}";

//             $.ajax({
//                 url: action_url,
//                 data: {
//                     'data': data
//                 },
//                 headers: {
//                     'X-CSRF-TOKEN': csrf
//                 },
//                 type: "POST",

//                 beforeSend: function() {
//                     $(".loader").fadeIn();
//                     $('.loader').css("visibility", "visible");
//                 },
//                 success: function(response) {

//                     //console.log("responce - admin - notification");
//                     $('.loader').css("visibility", "hidden");
//                     $(document).find(".offcanvas-body").html(response.html);
//                     $(".offcanvasNotification").addClass('show');
//                     $(".offcanvasNotification").css('visibility', 'visible');
//                     // document.getElementById('notificationoffcanvas').classList.toggle('show');
//                     // location.reload();
//     },
// });
// }

function readNotification() {
    $(".offcanvasNotification").addClass('show');
    $(".offcanvasNotification").css('visibility', 'visible');

    var data = 1;
    var action_url = "{{ route('read-admin-notification') }}";
    var csrf = "{{ csrf_token() }}";

    $.ajax({
        url: action_url,
        data: {
            'data': data
        },
        headers: {
            'X-CSRF-TOKEN': csrf
        },
        type: "POST",
        beforeSend: function() {
            $(".loader").fadeIn();
            $('.loader').css("visibility", "visible");
        },
        success: function(response) {
            $('.loader').css("visibility", "hidden");
            $(document).find(".offcanvas-body").html(response.html);
            $(".offcanvasNotification").addClass('show');
            $(".offcanvasNotification").css('visibility', 'visible');
        }
    });
}

// Function to periodically fetch notifications
// var lastNotificationCount = 0;

function fetchNotifications() {
    var action_url = "{{ route('read-admin-notification') }}";
    var csrf = "{{ csrf_token() }}";

    $.ajax({
        url: action_url,
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': csrf
        },
        data: {
            'data': 1
        },
        success: function(response) {
            $(document).find(".offcanvas-body").html(response.html);

            if (response.notificationListCount > 0) {
                $(".notification-no").text(response.notificationListCount).show();

                // 🔹 Play buzzer if count increased
                // if (response.notificationListCount > lastNotificationCount) {
                //     var audio = new Audio("{{ asset('sounds/buzzer.mp3') }}");
                //     audio.play();
                // }

            } else {
                $(".notification-no").hide();
            }

            // Update last count for next check
            // lastNotificationCount = response.notificationListCount;

        }
    });
}

// Call fetchNotifications every minute
setInterval(fetchNotifications, 15000); // 60000 milliseconds = 1 minute

</script>
